<?php

namespace Database\Factories;

use App\Models\CartItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CartItem>
 */
class CartItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'added_at' => now(),
            'cart_id' => ShoppingCartFactory::new(),
            'product_id' => ProductFactory::new(),
            'quantity' => fake()->numberBetween(1, 3),
        ];
    }
}
