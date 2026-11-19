<?php

namespace Database\Factories;

use App\Models\ShoppingCart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShoppingCart>
 */
class ShoppingCartFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new(),
        ];
    }
}
