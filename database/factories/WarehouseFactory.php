<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'address' => mb_substr(fake()->streetAddress(), 0, 101),
            'city' => mb_substr(fake()->city(), 0, 40),
            'country_id' => CountryFactory::new(),
            'name' => fake()->unique()->randomElement(Warehouse::ALLOWED_NAME),
        ];
    }
}
