# Laravel techniques in the e-commerce assignment

These examples come from the analytics queries, models, factories, and seeders in the project. Query numbers refer to the [e-commerce analytics report](ANALYTICS.md).

## 1. Eloquent relationships

An Eloquent relationship defines how a model is associated with records in another model.

[Official Laravel 13 documentation](https://laravel.com/docs/13.x/eloquent-relationships#one-to-many)

Project example in `app/Models/Order.php`.

```php
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id');
    }
```

An order can have several line items and links to one payment through the same foreign-key name, `order_id`, on the respective related tables. Named relationships centralize those associations for callers that would otherwise write foreign-key filters directly.

## 2. Local query scopes

A local scope packages reusable constraints that callers can chain onto an Eloquent query.

[Official Laravel 13 documentation](https://laravel.com/docs/13.x/eloquent#local-scopes)

Project example in `app/Models/Order.php`.

```php
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query
            ->whereHas('status', fn (Builder $status) => $status->whereIn('status_name', ['paid', 'shipped', 'delivered']))
            ->whereHas('payment', fn (Builder $payment) => $payment->where('status', 'completed'));
    }
```

The `successful()` scope gives all eight analytics queries the same definition of a successful order. It replaces repeating the status and payment conditions in each query.

## 3. Relationship existence filters

Eloquent’s `whereHas()` filters parent records by whether a related record meets the given conditions.

[Official Laravel 13 documentation](https://laravel.com/docs/13.x/eloquent-relationships#querying-relationship-existence)

Project example in `app/Analytics/ReturnBehavior.php`.

```php
        $orders = Order::successful()->select('id', 'user_id')
            ->withExists(['orderItems as has_return' => fn ($items) => $items->whereHas(
                'orderReturn', fn ($returns) => $returns->whereIn('status', ['approved', 'refunded'])
            )]);
```

Q7 uses `whereHas()` to find order items with an approved or refunded return. The enclosing `withExists()` records a yes-or-no result for the order, so multiple qualifying items still count as one returned order.

## 4. Relationship aggregates

Relationship aggregate methods such as `withCount()` and `withSum()` add scalar summaries of related rows to the main query.

[Official Laravel 13 documentation](https://laravel.com/docs/13.x/eloquent-relationships#aggregating-related-models)

Project example in `app/Analytics/CustomerOrders.php`.

```php
            ->withCount(['orders as order_count' => fn ($orders) => $orders->successful()])
            ->withSum(['orders as total_spend' => fn ($orders) => $orders->successful()], 'total')
```

Q1 and Q4 need the count and total value of each customer’s successful orders. These methods compute both values in SQL and avoid loading individual orders into PHP. The Query Builder alternative groups successful orders by customer first. See the [sample run results below](#q1-and-q4-sample-run) for both implementations.

## 5. Correlated select subqueries

A correlated select subquery uses a value from the outer query to return a value for each outer row.

[Official Laravel 13 documentation](https://laravel.com/docs/13.x/eloquent#advanced-subqueries)

Project example in `app/Analytics/CustomerOrders.php`.

```php
            ->addSelect([
                'last_order_date' => Order::successful()->select('created_at')->whereColumn('user_id', 'users.id')->latest('created_at')->latest('id')->limit(1),
                'last_order_total' => Order::successful()->select('total')->whereColumn('user_id', 'users.id')->latest('created_at')->latest('id')->limit(1),
            ])->toBase();
```

Q1 uses the latest order total and Q4 uses the latest order date. Both subqueries match the outer customer ID and sort by date and then ID so that tied dates resolve consistently. The grouped Query Builder alternative uses `MAX` for the date and an ordered `ARRAY_AGG` for the total. See the [sample run results below](#q1-and-q4-sample-run) for both implementations.

## 6. Grouped SQL aggregates

Query Builder’s `groupBy()` groups rows by a key so that SQL can calculate aggregate values for each group.

[Official Laravel 13 documentation](https://laravel.com/docs/13.x/queries#grouping)

Project example in `app/Analytics/CustomerOrders.php`.

```php
        $spend = DB::query()->fromSub($orders, 'successful_orders')
            ->select('user_id')
            ->selectRaw('COUNT(*) AS order_count, SUM(total) AS total_spend, MAX(created_at) AS last_order_date, (ARRAY_AGG(total ORDER BY created_at DESC, id DESC))[1] AS last_order_total')
            ->groupBy('user_id');
```

The grouped Q1 and Q4 implementation produces one summary row per customer with successful orders before joining to users. The alternative adds relationship aggregates and correlated subqueries to the customer query. See the [sample run results below](#q1-and-q4-sample-run) for both implementations.

## 7. Joining subqueries

Query Builder’s `joinSub()` joins a query to the result of another query under a table alias.

[Official Laravel 13 documentation](https://laravel.com/docs/13.x/queries#subquery-joins)

Project example in `app/Analytics/PopularButPoorlyRated.php`.

```php
        return DB::table('products')->joinSub($sales, 'sales', 'sales.product_id', '=', 'products.id')
            ->joinSub($ratings, 'ratings', 'ratings.product_id', '=', 'products.id')
```

Q6 joins per-product sales and review summaries after each has been aggregated. Joining the underlying sales lines and reviews directly could multiply rows and inflate sales or review counts.

## 8. Raw SQL expressions

Query Builder’s raw expression methods embed SQL fragments where a query needs database-specific syntax or functions.

[Official Laravel 13 documentation](https://laravel.com/docs/13.x/queries#raw-methods)

Project example in `app/Analytics/ChurnRisk.php`.

```php
            ->where('order_count', '>=', 2)
            ->whereRaw("last_order_date < (SELECT MAX(created_at) - INTERVAL '90 days' FROM orders)")
            ->orderBy('last_order_date')->orderBy('id')->get();
```

Q4 uses PostgreSQL interval arithmetic to compare the customer’s last successful order with a cutoff based on the latest order in the entire table. The expression uses a fixed 90-day literal and replaces a separate lookup and cutoff calculation in PHP.

## 9. Model factories

An Eloquent factory defines default attributes and related records for generating model instances.

[Official Laravel 13 documentation](https://laravel.com/docs/13.x/eloquent-factories#defining-model-factories)

Project example in `database/factories/OrderFactory.php`.

```php
    public function definition(): array
    {
        return [
            'billing_address_id' => AddressFactory::new(),
            'coupon_id' => null,
            'created_at' => now(),
            'discount_amount' => 0,
            'order_status_id' => OrderStatusFactory::new(),
            'shipping_address_id' => AddressFactory::new(),
            'shipping_cost' => 5,
            'shipping_method_id' => ShippingMethodFactory::new(),
            'subtotal' => 100,
            'tax_amount' => 5,
            'total' => 110,
            'user_id' => UserFactory::new(),
        ];
    }
```

The order factory supplies consistent monetary defaults and factories for required related records. Tests can override the fields they care about without defining an entire order and all its dependencies in every fixture.

## 10. Seeder orchestration

A Laravel seeder populates database data and can use `call()` to run other seeders in a defined order.

[Official Laravel 13 documentation](https://laravel.com/docs/13.x/seeding#calling-additional-seeders)

Project example in `database/seeders/DatabaseSeeder.php`.

```php
    public function run(): void
    {
        $this->call(ReferenceDataSeeder::class);

        if (app()->environment(['local', 'development'])) {
            $this->call(DevelopmentDataSeeder::class);
        }
    }
```

The default seeder loads reference data first and runs the development seeder only in the local or development environment. Delegation keeps the two sets of seed operations in their own classes.

## Q1 and Q4 sample run

| Query | Eloquent time | Eloquent SQL queries | Query Builder time | Query Builder SQL queries |
|---|---:|---:|---:|---:|
| Q1 | 48.34 ms | 1 | 6.52 ms | 1 |
| Q4 | 45.06 ms | 1 | 4.77 ms | 1 |

Each timing and SQL query count comes from a single run of that implementation on the official sample dataset. Timings vary between runs.

See the [analytics comparison](ANALYTICS.md#q1-and-q4-implementation-comparison) for the commands used to compare the implementations.
