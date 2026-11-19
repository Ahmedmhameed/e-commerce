<?php

namespace Database\Factories;

use App\Models\OrderReturn;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderReturn>
 */
class OrderReturnFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_item_id' => OrderItemFactory::new(),
            'processed_at' => now(),
            'quantity' => 1,
            'reason_id' => ReturnReasonFactory::new(),
            'requested_at' => now()->subDay(),
            'status' => fake()->randomElement(OrderReturn::ALLOWED_STATUS),
        ];
    }
}
