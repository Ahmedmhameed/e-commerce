<?php

namespace Database\Factories;

use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShippingMethod>
 */
class ShippingMethodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'base_cost' => fake()->randomFloat(2, 0, 35),
            'carrier' => fake()->randomElement(ShippingMethod::ALLOWED_CARRIER),
            'estimated_days' => fake()->numberBetween(1, 7),
            'name' => fake()->unique()->randomElement(ShippingMethod::ALLOWED_NAME),
        ];
    }
}
