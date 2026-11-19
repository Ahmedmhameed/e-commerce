<?php

namespace Database\Factories;

use App\Models\ProductAttributeValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductAttributeValue>
 */
class ProductAttributeValueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'attribute_id' => ProductAttributeFactory::new(),
            'product_id' => ProductFactory::new(),
            'value' => fake()->safeColorName(),
        ];
    }
}
