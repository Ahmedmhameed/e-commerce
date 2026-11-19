<?php

namespace Database\Factories;

use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductImage>
 */
class ProductImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'alt_text' => fake()->randomElement(ProductImage::ALLOWED_ALT_TEXT),
            'product_id' => ProductFactory::new(),
            'sort_order' => fake()->numberBetween(1, 6),
            'url' => 'https://example.test/'.fake()->unique()->regexify('[a-z0-9]{14}'),
        ];
    }
}
