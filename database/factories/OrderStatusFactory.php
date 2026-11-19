<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderStatus>
 */
class OrderStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'description' => fake()->randomElement(OrderStatus::ALLOWED_DESCRIPTION),
            'status_name' => fake()->unique()->randomElement(OrderStatus::ALLOWED_STATUS_NAME),
        ];
    }
}
