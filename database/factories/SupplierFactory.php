<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contact_name' => mb_substr(fake()->name(), 0, 62),
            'email' => fake()->unique()->safeEmail(),
            'name' => mb_substr(fake()->company(), 0, 114),
            'phone' => fake()->e164PhoneNumber(),
            'website' => 'https://example.test/'.fake()->lexify('????????'),
        ];
    }
}
