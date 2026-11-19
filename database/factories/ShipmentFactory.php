<?php

namespace Database\Factories;

use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shipment>
 */
class ShipmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'delivered_at' => now(),
            'order_id' => OrderFactory::new(),
            'shipped_at' => now()->subDay(),
            'tracking_number' => fake()->unique()->regexify('[A-Z0-9]{18}'),
        ];
    }
}
