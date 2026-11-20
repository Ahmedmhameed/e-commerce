# Laravel techniques in the e-commerce assignment

These examples use the catalog and review tables from the 1K PostgreSQL e-commerce sample. Each class exposes a `run()` method. To run an example against the loaded sample, use the application’s sample-database connection and call the class from Artisan Tinker.

```php
(new \App\Analytics\Examples\JoinSub)->run();
```

`When` accepts an optional category ID, `OfMany` accepts an existing product ID, and `ChunkById` accepts an optional positive batch size.

The ten tests in [TechniquesTest.php](../tests/Feature/TechniquesTest.php) use deterministic factory fixtures in `ecommerce_test`. Expected IDs, counts, and ratings are derived from the fixture inputs.

## DB Query Builder

### `joinSub`

Joins a query to the rows produced by a subquery.

Prefer it when the joined input needs to be grouped before the join. This makes the one-row-per-product requirement explicit and keeps individual review rows out of the outer result.

[Laravel 13 documentation](https://laravel.com/docs/13.x/queries#subquery-joins)

**Dataset question.** Which products have reviews, and how many reviews does each have?

**Source.** [`app/Analytics/Examples/JoinSub.php`](../app/Analytics/Examples/JoinSub.php)

```php
    public function run(): Collection
    {
        $reviewCounts = DB::table('product_reviews')
            ->select('product_id')
            ->selectRaw('COUNT(*) AS review_count')
            ->groupBy('product_id');

        return DB::table('products')
            ->joinSub($reviewCounts, 'reviews', 'reviews.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', 'reviews.review_count')
            ->orderBy('products.id')
            ->get();
    }
```

### `whereExists`

Keeps an outer row when a correlated subquery finds at least one matching row.

Prefer it over an inner join when only the presence of a match matters. A product with multiple five-star reviews can then appear once without a separate deduplication step.

[Laravel 13 documentation](https://laravel.com/docs/13.x/queries#where-exists-clauses)

**Dataset question.** Which products have received at least one five-star review?

**Source.** [`app/Analytics/Examples/WhereExists.php`](../app/Analytics/Examples/WhereExists.php)

```php
    public function run(): Collection
    {
        $highRatings = DB::table('product_reviews')
            ->select('product_reviews.id')
            ->whereColumn('product_reviews.product_id', 'products.id')
            ->where('rating', 5);

        return DB::table('products')
            ->whereExists($highRatings)
            ->orderBy('id')
            ->pluck('name', 'id');
    }
```

### `selectSub`

Adds a scalar subquery result as a named column in the SELECT list.

Prefer it over a separate lookup per product when the report needs one related value per row. Limit the subquery to one row and specify a tie-breaker when selecting a particular review.

[Laravel 13 documentation](https://laravel.com/docs/13.x/eloquent#advanced-subqueries)

[Laravel 13 API reference for `selectSub`](https://api.laravel.com/docs/13.x/Illuminate/Database/Query/Builder.html#method_selectSub)

**Dataset question.** What rating did each product receive in its latest review, if any?

**Source.** [`app/Analytics/Examples/SelectSub.php`](../app/Analytics/Examples/SelectSub.php)

```php
    public function run(): Collection
    {
        $latestRating = DB::table('product_reviews')
            ->select('rating')
            ->whereColumn('product_id', 'products.id')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(1);

        return DB::table('products')
            ->select('products.id', 'products.name')
            ->selectSub($latestRating, 'latest_rating')
            ->orderBy('products.id')
            ->get();
    }
```

### `when`

Applies a query callback when its condition is true.

Prefer it over building two separate queries when a single optional filter distinguishes them. The active-product filter and result ordering can then stay in one shared chain.

[Laravel 13 documentation](https://laravel.com/docs/13.x/queries#conditional-clauses)

**Dataset question.** Which active products are available, optionally within a given category?

**Source.** [`app/Analytics/Examples/When.php`](../app/Analytics/Examples/When.php)

```php
    public function run(?int $categoryId = null): Collection
    {
        return DB::table('products')
            ->where('is_active', true)
            ->when($categoryId !== null, fn (Builder $query) => $query->where('category_id', $categoryId))
            ->orderBy('id')
            ->pluck('name', 'id');
    }
```

## Eloquent ORM

### `withAggregate`

Adds a scalar aggregate of a model relationship to the selected model attributes.

Prefer it over loading every review and reducing the collection in PHP when the result needs only an aggregate. For a fixed maximum in production, the dedicated withMax method expresses the same intent more directly.

[Laravel 13 documentation](https://laravel.com/docs/13.x/eloquent-relationships#other-aggregate-functions)

[Laravel 13 API reference for `withAggregate`](https://api.laravel.com/docs/13.x/Illuminate/Database/Eloquent/Concerns/QueriesRelationships.html#method_withAggregate)

**Dataset question.** What is the highest rating each product has received?

**Source.** [`app/Analytics/Examples/WithAggregate.php`](../app/Analytics/Examples/WithAggregate.php)

```php
    public function run(): Collection
    {
        return Product::query()
            ->select('id', 'name')
            ->withAggregate('productReviews as highest_rating', 'rating', 'max')
            ->orderBy('id')
            ->get();
    }
```

### `chunkById`

Processes matching models in batches using the last processed ID to locate the next batch.

Prefer it over get when a task must visit every model and loading the whole result would use too much memory. The count here illustrates batch processing, while a SQL count is simpler when only the total is needed.

[Laravel 13 documentation](https://laravel.com/docs/13.x/eloquent#chunking-results)

**Dataset question.** How many active products are encountered during a batched catalog scan?

**Source.** [`app/Analytics/Examples/ChunkById.php`](../app/Analytics/Examples/ChunkById.php)

```php
    public function run(int $batchSize = 100): int
    {
        $activeCount = 0;

        Product::query()
            ->select('id')
            ->where('is_active', true)
            ->chunkById($batchSize, function (Collection $products) use (&$activeCount): void {
                $activeCount += $products->count();
            });

        return $activeCount;
    }
```

### `whereRelation`

Filters models using a single condition on a named relationship.

Prefer it over a whereHas callback when one related-column comparison fully describes the filter. The shorter form keeps the rating condition visible in the product query.

[Laravel 13 documentation](https://laravel.com/docs/13.x/eloquent-relationships#inline-relationship-existence-queries)

**Dataset question.** Which products have at least one review rated two stars or below?

**Source.** [`app/Analytics/Examples/WhereRelation.php`](../app/Analytics/Examples/WhereRelation.php)

```php
    public function run(): Collection
    {
        return Product::query()
            ->whereRelation('productReviews', 'rating', '<=', 2)
            ->orderBy('id')
            ->get(['id', 'name']);
    }
```

## Eloquent Relationships

### `whereDoesntHave`

Keeps models whose relationship has no rows that meet the given conditions.

Prefer it over filtering loaded reviews in PHP when the database can apply the absence test. A constrained absence check includes both products with only lower ratings and products with no reviews.

[Laravel 13 documentation](https://laravel.com/docs/13.x/eloquent-relationships#querying-relationship-absence)

**Dataset question.** Which products have never received a rating of four stars or above?

**Source.** [`app/Analytics/Examples/WhereDoesntHave.php`](../app/Analytics/Examples/WhereDoesntHave.php)

```php
    public function run(): Collection
    {
        return Product::query()
            ->whereDoesntHave('productReviews', fn (Builder $reviews) => $reviews->where('rating', '>=', 4))
            ->orderBy('id')
            ->get(['id', 'name']);
    }
```

### `withAvg`

Adds the average of a related column to each selected parent model.

Prefer it over fetching every rating when the report needs only the mean. The aggregate returns null for an unreviewed product, which keeps missing ratings distinguishable from numeric values.

[Laravel 13 documentation](https://laravel.com/docs/13.x/eloquent-relationships#other-aggregate-functions)

**Dataset question.** What is each product’s average review rating?

**Source.** [`app/Analytics/Examples/WithAvg.php`](../app/Analytics/Examples/WithAvg.php)

```php
    public function run(): Collection
    {
        return Product::query()
            ->select('id', 'name')
            ->withAvg('productReviews as average_rating', 'rating')
            ->orderBy('id')
            ->get();
    }
```

### `ofMany`

Selects a single related model from a one-to-many relationship using aggregate selection criteria.

Prefer it over sorting a loaded collection when the caller needs a single related model selected by the database. Use multiple criteria when the primary criterion can tie and the chosen review must be deterministic.

[Laravel 13 documentation](https://laravel.com/docs/13.x/eloquent-relationships#has-one-of-many)

**Dataset question.** Which review has the highest rating for a selected product, with the greatest review ID breaking ties?

**Source.** [`app/Analytics/Examples/OfMany.php`](../app/Analytics/Examples/OfMany.php)

```php
    public function run(int $productId): ?ProductReview
    {
        return Product::query()
            ->findOrFail($productId)
            ->productReviews()
            ->one()
            ->ofMany(['rating' => 'max', 'id' => 'max'])
            ->first();
    }
```
## Sample database verification

The results below were checked against independent SQL on the imported 1K PostgreSQL sample. Product-specific checks used product ID 1 and the category filter used category ID 20.

| Technique | Independently verified result |
|---|---|
| `joinSub` | Product 1 has 3 reviews |
| `whereExists` | 26 products have a five-star review |
| `selectSub` | Product 1's latest rating is 5 |
| `when` | Category 20 has 12 active products |
| `withAggregate` | Product 1's highest rating is 5 |
| `chunkById` | The catalog has 369 active products |
| `whereRelation` | 15 products have a rating of two or below |
| `whereDoesntHave` | 365 products have no rating of four or above |
| `withAvg` | Product 1's average rating is 3.6666666666666667 |
| `ofMany` | Review 15 wins for product 1 |
