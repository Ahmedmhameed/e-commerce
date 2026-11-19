<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'discount_amount' => 0,
            'order_id' => OrderFactory::new(),
            'product_id' => ProductFactory::new(),
            'quantity' => fake()->numberBetween(1, 3),
            'tax_amount' => 0,
            'unit_price' => fake()->randomFloat(2, 3, 160),
        ];
    }
}
