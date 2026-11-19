<?php

namespace Database\Factories;

use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductReview>
 */
class ProductReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'comment' => fake()->randomElement(ProductReview::ALLOWED_COMMENT),
            'created_at' => now(),
            'product_id' => ProductFactory::new(),
            'rating' => fake()->numberBetween(1, 5),
            'title' => fake()->randomElement(ProductReview::ALLOWED_TITLE),
            'user_id' => UserFactory::new(),
        ];
    }
}
