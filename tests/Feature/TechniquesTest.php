<?php

namespace Tests\Feature;

use App\Analytics\Examples\ChunkById;
use App\Analytics\Examples\JoinSub;
use App\Analytics\Examples\OfMany;
use App\Analytics\Examples\SelectSub;
use App\Analytics\Examples\When;
use App\Analytics\Examples\WhereDoesntHave;
use App\Analytics\Examples\WhereExists;
use App\Analytics\Examples\WhereRelation;
use App\Analytics\Examples\WithAggregate;
use App\Analytics\Examples\WithAvg;
use App\Models\Product;
use Database\Factories\ProductFactory;
use Database\Factories\ProductReviewFactory;
use Tests\TestCase;

class TechniquesTest extends TestCase
{
    protected Product $reviewed;

    protected Product $inactive;

    protected Product $unreviewed;

    protected int $winningReviewId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewed = ProductFactory::new()->create();
        $this->inactive = ProductFactory::new()->create([
            'category_id' => $this->reviewed->category_id,
            'is_active' => false,
        ]);
        $this->unreviewed = ProductFactory::new()->create();

        foreach ([5, 5, 2] as $index => $rating) {
            $review = ProductReviewFactory::new()->create([
                'product_id' => $this->reviewed->id,
                'rating' => $rating,
                'created_at' => sprintf('2025-01-%02d 00:00:00', $index + 1),
            ]);
            if ($index === 1) {
                $this->winningReviewId = $review->id;
            }
        }

        ProductReviewFactory::new()->create([
            'product_id' => $this->inactive->id,
            'rating' => 4,
        ]);
    }

    public function test_join_sub_returns_counts_for_reviewed_products_only(): void
    {
        $counts = (new JoinSub)->run()->pluck('review_count', 'id')->map(fn ($count) => (int) $count);

        $this->assertSame([$this->reviewed->id => 3, $this->inactive->id => 1], $counts->all());
    }

    public function test_where_exists_returns_each_five_star_product_once(): void
    {
        $this->assertSame([$this->reviewed->id => $this->reviewed->name], (new WhereExists)->run()->all());
    }

    public function test_select_sub_returns_latest_rating_and_retains_unreviewed_products(): void
    {
        $ratings = (new SelectSub)->run()->pluck('latest_rating', 'id')->all();

        $this->assertSame([
            $this->reviewed->id => 2,
            $this->inactive->id => 4,
            $this->unreviewed->id => null,
        ], $ratings);
    }

    public function test_when_applies_the_category_filter_only_when_supplied(): void
    {
        $example = new When;

        $this->assertSame([$this->reviewed->id, $this->unreviewed->id], $example->run()->keys()->all());
        $this->assertSame([$this->reviewed->id], $example->run($this->reviewed->category_id)->keys()->all());
    }

    public function test_with_aggregate_selects_the_highest_rating(): void
    {
        $ratings = (new WithAggregate)->run()->keyBy('id');

        $this->assertEquals(5, $ratings[$this->reviewed->id]->highest_rating);
        $this->assertEquals(4, $ratings[$this->inactive->id]->highest_rating);
        $this->assertNull($ratings[$this->unreviewed->id]->highest_rating);
    }

    public function test_chunk_by_id_counts_active_products_across_batches(): void
    {
        $this->assertSame(2, (new ChunkById)->run(1));
    }

    public function test_where_relation_finds_products_with_low_ratings(): void
    {
        $this->assertSame([$this->reviewed->id], (new WhereRelation)->run()->modelKeys());
    }

    public function test_where_doesnt_have_includes_unreviewed_products_and_excludes_high_ratings(): void
    {
        ProductReviewFactory::new()->create([
            'product_id' => $this->unreviewed->id,
            'rating' => 3,
        ]);
        $noReviews = ProductFactory::new()->create();

        $this->assertSame([$this->unreviewed->id, $noReviews->id], (new WhereDoesntHave)->run()->modelKeys());
    }

    public function test_with_avg_computes_the_mean_and_leaves_missing_ratings_null(): void
    {
        $ratings = (new WithAvg)->run()->keyBy('id');

        $this->assertSame(4.0, (float) $ratings[$this->reviewed->id]->average_rating);
        $this->assertSame(4.0, (float) $ratings[$this->inactive->id]->average_rating);
        $this->assertNull($ratings[$this->unreviewed->id]->average_rating);
    }

    public function test_of_many_selects_the_highest_rating_and_breaks_ties_by_id(): void
    {
        $example = new OfMany;

        $this->assertSame($this->winningReviewId, $example->run($this->reviewed->id)?->id);
        $this->assertNull($example->run($this->unreviewed->id));
    }
}
