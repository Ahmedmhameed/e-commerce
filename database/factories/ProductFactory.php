<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'brand_id' => BrandFactory::new(),
            'category_id' => CategoryFactory::new(),
            'compare_at_price' => null,
            'created_at' => now(),
            'description' => fake()->randomElement(Product::ALLOWED_DESCRIPTION),
            'is_active' => true,
            'name' => mb_substr(fake()->words(3, true), 0, 59),
            'price' => fake()->randomFloat(2, 3, 160),
            'sku' => fake()->unique()->regexify('[A-Z0-9]{12}'),
            'slug' => fake()->unique()->regexify('[a-z0-9]{10}'),
        ];
    }
}
