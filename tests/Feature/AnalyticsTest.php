<?php

namespace Tests\Feature;

use App\Analytics\BestProductPerCategory;
use App\Analytics\ChurnRisk;
use App\Analytics\FrequentlyBoughtTogether;
use App\Analytics\HighValueCustomers;
use App\Analytics\MonthlyRevenueGrowth;
use App\Analytics\PopularButPoorlyRated;
use App\Analytics\ProductsBelowCategoryAverage;
use App\Analytics\ReturnBehavior;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ReturnReason;
use App\Models\ShippingMethod;
use App\Models\User;
use Database\Factories\OrderFactory;
use Database\Factories\OrderItemFactory;
use Database\Factories\OrderReturnFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\ProductFactory;
use Database\Factories\ProductReviewFactory;
use Database\Factories\UserFactory;
use Database\Seeders\ReferenceDataSeeder;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ReferenceDataSeeder::class);
    }

    public function test_high_value_customers_compare_spend_across_buyers_and_show_last_order_total(): void
    {
        $buyer = UserFactory::new()->create(['first_name' => 'Alex', 'last_name' => 'Buyer']);
        $other = UserFactory::new()->create();
        UserFactory::new()->create();

        $this->order($buyer, '2025-01-01', 100);
        $this->order($buyer, '2025-01-02', 110);
        $this->order($buyer, '2025-01-02', 120);
        $this->order($other, '2025-01-01', 10);
        $this->order($buyer, '2025-01-03', 999, 'paid', 'failed');
        $this->order($buyer, '2025-01-04', 999, 'cancelled');

        $query = new HighValueCustomers;
        $rows = $query->run();

        $this->assertCount(1, $rows);
        $this->assertSame('Alex Buyer', $rows[0]->name);
        $this->assertEquals(3, $rows[0]->order_count);
        $this->assertEquals(330, $rows[0]->total_spend);
        $this->assertEquals(120, $rows[0]->last_order_total);
    }

    public function test_latest_successful_order_prefers_the_higher_id_at_the_same_timestamp(): void
    {
        $buyer = UserFactory::new()->create();
        $other = UserFactory::new()->create();
        $this->order($buyer, '2025-01-01', 200);
        $this->order($buyer, '2025-01-02', 120);
        $latest = $this->order($buyer, '2025-01-02', 90);
        $this->order($buyer, '2025-01-02', 500, payment: 'failed');
        $this->order($buyer, '2025-01-03', 600, status: 'pending');
        $this->order($other, '2025-01-01', 10);

        $this->assertSame($latest->id, $buyer->latestSuccessfulOrder()->firstOrFail()->id);
        $this->assertEquals(90, (new HighValueCustomers)->run()->sole()->last_order_total);
    }

    public function test_product_pairs_count_distinct_orders_without_self_or_reversed_pairs(): void
    {
        $user = UserFactory::new()->create();
        $first = ProductFactory::new()->create(['name' => 'Alpha']);
        $second = ProductFactory::new()->create(['name' => 'Beta', 'category_id' => $first->category_id]);

        foreach (['2025-01-01', '2025-01-02'] as $date) {
            $order = $this->order($user, $date);
            $this->line($order, $first);
            $this->line($order, $first);
            $this->line($order, $second);
        }

        $order = $this->order($user, '2025-01-03', status: 'cancelled');
        $this->line($order, $first);
        $this->line($order, $second);

        $rows = (new FrequentlyBoughtTogether)->run();
        $this->assertCount(1, $rows);
        $this->assertSame('Alpha', $rows[0]->product_one);
        $this->assertSame('Beta', $rows[0]->product_two);
        $this->assertEquals(2, $rows[0]->order_count);
    }

    public function test_product_pair_name_ties_determine_the_fifth_place(): void
    {
        $user = UserFactory::new()->create();
        $products = collect(['Zulu', 'Alpha', 'Delta', 'Charlie', 'Bravo'])
            ->mapWithKeys(fn (string $name) => [$name => ProductFactory::new()->create(['name' => $name])]);

        foreach ([
            ['Zulu', 'Delta', 2],
            ['Zulu', 'Charlie', 2],
            ['Alpha', 'Delta', 2],
            ['Delta', 'Charlie', 2],
            ['Zulu', 'Bravo', 1],
            ['Alpha', 'Charlie', 1],
            ['Alpha', 'Bravo', 1],
        ] as [$first, $second, $count]) {
            for ($index = 0; $index < $count; $index++) {
                $order = $this->order($user, '2025-01-01');
                $this->line($order, $products[$first]);
                $this->line($order, $products[$second]);
            }
        }

        $rows = (new FrequentlyBoughtTogether)->run();

        $this->assertSame([
            ['Alpha', 'Delta', 2],
            ['Delta', 'Charlie', 2],
            ['Zulu', 'Charlie', 2],
            ['Zulu', 'Delta', 2],
            ['Alpha', 'Bravo', 1],
        ], $rows->map(fn ($row) => [$row->product_one, $row->product_two, (int) $row->order_count])->all());
    }

    public function test_category_average_excludes_unsold_products_and_subtracts_only_returned_units(): void
    {
        $order = $this->order(UserFactory::new()->create(), '2025-01-01');
        $below = ProductFactory::new()->create(['name' => 'Below']);
        $above = ProductFactory::new()->create(['category_id' => $below->category_id]);
        ProductFactory::new()->create(['category_id' => $below->category_id]);

        $line = $this->line($order, $below, 3, 10, 3);
        $this->returned($line, 'approved');
        $this->returned($this->line($order, $above, 3, 10), 'requested');

        $rows = (new ProductsBelowCategoryAverage)->run();
        $this->assertCount(1, $rows);
        $this->assertSame('Below', $rows[0]->product);
        $this->assertEquals(18, $rows[0]->revenue);
        $this->assertEquals(24, $rows[0]->category_average);
    }

    public function test_fully_returned_product_contributes_zero_revenue_to_category_average(): void
    {
        $order = $this->order(UserFactory::new()->create(), '2025-01-01');
        $fullyReturned = ProductFactory::new()->create(['name' => 'Fully returned']);
        $kept = ProductFactory::new()->create(['category_id' => $fullyReturned->category_id]);
        $this->returned($this->line($order, $fullyReturned, 1, 10), 'refunded');
        $this->line($order, $kept, 3, 20);

        $rows = (new ProductsBelowCategoryAverage)->run();

        $this->assertCount(1, $rows);
        $this->assertSame('Fully returned', $rows[0]->product);
        $this->assertSame('0.00', (string) $rows[0]->revenue);
        $this->assertEquals(30, $rows[0]->category_average);
    }

    public function test_churn_uses_latest_order_including_unsuccessful_orders_and_excludes_cutoff_day(): void
    {
        $old = UserFactory::new()->create(['first_name' => 'Old', 'last_name' => 'Buyer']);
        $boundary = UserFactory::new()->create();
        $this->order($old, '2024-12-01', 10);
        $this->order($old, '2024-12-31', 20);
        $this->order($boundary, '2024-12-01');
        $this->order($boundary, '2025-01-01');
        $this->order($old, '2025-04-01', status: 'pending');

        $query = new ChurnRisk;
        $rows = $query->run();
        $this->assertCount(1, $rows);
        $this->assertSame('Old Buyer', $rows[0]->name);
        $this->assertStringStartsWith('2024-12-31', $rows[0]->last_order_date);
        $this->assertEquals(2, $rows[0]->order_count);
        $this->assertEquals(30, $rows[0]->total_spend);
    }

    public function test_successful_order_at_the_exact_churn_cutoff_excludes_the_customer(): void
    {
        $buyer = UserFactory::new()->create();
        $this->order($buyer, '2024-12-31 12:00:00');
        $this->order($buyer, '2025-01-01 12:00:00');
        $this->order($buyer, '2025-04-01 12:00:00', status: 'pending');

        $this->assertCount(0, (new ChurnRisk)->run());
    }

    public function test_monthly_growth_fills_gaps_and_returns_null_when_previous_revenue_is_zero(): void
    {
        $user = UserFactory::new()->create();
        $this->order($user, '2025-01-15', 100);
        $this->order($user, '2025-03-10', 200);
        $this->order($user, '2025-04-10', 300);

        $rows = (new MonthlyRevenueGrowth)->run();
        $this->assertSame(['2025-01', '2025-02', '2025-03', '2025-04'], $rows->pluck('month')->all());
        $this->assertEquals([100, 0, 200, 300], $rows->pluck('revenue')->all());
        $this->assertNull($rows[0]->growth_percentage);
        $this->assertEquals(-100, $rows[1]->growth_percentage);
        $this->assertNull($rows[2]->growth_percentage);
        $this->assertEquals(50, $rows[3]->growth_percentage);
    }

    public function test_popular_products_use_mean_of_product_averages_and_exclude_unreviewed_products(): void
    {
        $user = UserFactory::new()->create();
        $poor = ProductFactory::new()->create(['name' => 'Poor']);
        $unreviewed = ProductFactory::new()->create(['category_id' => $poor->category_id]);
        $rated = ProductFactory::new()->create(['category_id' => $poor->category_id]);
        $threshold = ProductFactory::new()->create(['category_id' => $poor->category_id]);

        foreach ([3, 3, 3, 1] as $index => $quantity) {
            $order = $this->order($user, '2025-01-0'.($index + 1));
            $this->line($order, $poor, $quantity);
            $this->line($order, $unreviewed, $quantity);
            $this->line($order, $threshold, $quantity);
        }

        foreach ([[$poor, 1], [$poor, 3], [$rated, 5], [$threshold, 3], [$threshold, 3], [$threshold, 4]] as [$product, $rating]) {
            ProductReviewFactory::new()->create(['user_id' => $user->id, 'product_id' => $product->id, 'rating' => $rating]);
        }

        $rows = (new PopularButPoorlyRated)->run();
        $this->assertCount(2, $rows);
        $this->assertSame('Poor', $rows[0]->product);
        $this->assertEquals(10, $rows[0]->units_sold);
        $this->assertEquals(2, $rows[0]->average_rating);
        $this->assertEquals(2, $rows[0]->review_count);
        $this->assertSame($threshold->name, $rows[1]->product);
    }

    public function test_popular_products_sum_quantities_and_apply_the_ten_unit_threshold(): void
    {
        $user = UserFactory::new()->create();
        $qualifying = ProductFactory::new()->create(['name' => 'Ten units']);
        $belowThreshold = ProductFactory::new()->create(['category_id' => $qualifying->category_id]);
        $highlyRated = ProductFactory::new()->create(['category_id' => $qualifying->category_id]);

        foreach ([3, 2, 3, 2] as $index => $quantity) {
            $order = $this->order($user, '2025-01-0'.($index + 1));
            $this->line($order, $qualifying, $quantity);
            $this->line($order, $belowThreshold, $index === 3 ? 1 : $quantity);
        }

        foreach ([[$qualifying, 2], [$belowThreshold, 2], [$highlyRated, 5]] as [$product, $rating]) {
            ProductReviewFactory::new()->create(['user_id' => $user->id, 'product_id' => $product->id, 'rating' => $rating]);
        }

        $rows = (new PopularButPoorlyRated)->run();

        $this->assertSame(['Ten units'], $rows->pluck('product')->all());
        $this->assertEquals(10, $rows->first()->units_sold);
    }

    public function test_unreviewed_product_does_not_enter_results_or_lower_the_rating_benchmark(): void
    {
        $user = UserFactory::new()->create();
        $lowerRated = ProductFactory::new()->create(['name' => 'Three stars']);
        $higherRated = ProductFactory::new()->create(['name' => 'Five stars', 'category_id' => $lowerRated->category_id]);
        $unreviewed = ProductFactory::new()->create(['name' => 'No reviews', 'category_id' => $lowerRated->category_id]);
        foreach ([3, 3, 3, 1] as $quantity) {
            $order = $this->order($user, '2025-01-01');
            $this->line($order, $lowerRated, $quantity);
            $this->line($order, $unreviewed, $quantity);
        }
        ProductReviewFactory::new()->create(['user_id' => $user->id, 'product_id' => $lowerRated->id, 'rating' => 3]);
        ProductReviewFactory::new()->create(['user_id' => $user->id, 'product_id' => $higherRated->id, 'rating' => 5]);

        $query = new PopularButPoorlyRated;
        $rows = $query->run();

        $this->assertSame(['Three stars'], $rows->pluck('product')->all());
        $this->assertSame([
            'product' => 'Three stars',
            'units_sold' => 10,
            'review_count' => 1,
            'average_rating' => '3.00',
        ], (array) $rows->sole());
    }

    public function test_return_rate_counts_orders_not_lines_and_ignores_requested_returns(): void
    {
        $user = UserFactory::new()->create();
        $product = ProductFactory::new()->create();
        $first = $this->order($user, '2025-01-01');
        $second = $this->order($user, '2025-01-02');
        $third = $this->order($user, '2025-01-03');

        $this->returned($this->line($first, $product), 'approved');
        $this->returned($this->line($first, $product), 'refunded');
        $this->returned($this->line($second, $product), 'refunded');
        $this->returned($this->line($third, $product), 'requested');

        $rows = (new ReturnBehavior)->run();
        $this->assertCount(1, $rows);
        $this->assertEquals(3, $rows[0]->total_orders);
        $this->assertEquals(2, $rows[0]->orders_with_returns);
        $this->assertEquals(66.67, $rows[0]->return_rate);
    }

    public function test_best_product_per_category_preserves_revenue_ties(): void
    {
        $order = $this->order(UserFactory::new()->create(), '2025-01-01');
        $alpha = ProductFactory::new()->create(['name' => 'Alpha']);
        $beta = ProductFactory::new()->create(['name' => 'Beta', 'category_id' => $alpha->category_id]);
        $other = ProductFactory::new()->create(['name' => 'Other', 'category_id' => $alpha->category_id]);
        $this->line($order, $alpha, 2, 10);
        $this->line($order, $beta, 1, 20);
        $this->line($order, $other, 1, 10);

        $rows = (new BestProductPerCategory)->run();
        $this->assertSame(['Alpha', 'Beta'], $rows->pluck('product')->all());
        $this->assertEquals([20, 20], $rows->pluck('revenue')->all());
    }

    private function order(User $user, string $date, int $total = 100, string $status = 'paid', string $payment = 'completed'): Order
    {
        $order = OrderFactory::new()->create([
            'user_id' => $user->id,
            'created_at' => $date,
            'total' => $total,
            'order_status_id' => OrderStatus::query()->where('status_name', $status)->firstOrFail()->id,
            'shipping_method_id' => ShippingMethod::query()->where('name', 'standard')->firstOrFail()->id,
        ]);

        PaymentFactory::new()->create([
            'order_id' => $order->id,
            'payment_method_id' => PaymentMethod::query()->where('name', 'credit_card')->firstOrFail()->id,
            'status' => $payment,
        ]);

        return $order;
    }

    private function line(Order $order, Product $product, int $quantity = 1, int $price = 10, int $discount = 0): OrderItem
    {
        return OrderItemFactory::new()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $price,
            'discount_amount' => $discount,
        ]);
    }

    private function returned(OrderItem $item, string $status): void
    {
        OrderReturnFactory::new()->create([
            'order_item_id' => $item->id,
            'reason_id' => ReturnReason::query()->where('reason_text', 'Does not fit')->firstOrFail()->id,
            'status' => $status,
        ]);
    }
}
