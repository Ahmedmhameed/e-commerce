<?php

namespace Database\Factories;

use App\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaxRate>
 */
class TaxRateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'country_id' => CountryFactory::new(),
            'rate' => fake()->randomFloat(4, 0.02, 0.10),
            'state_province' => fake()->stateAbbr(),
        ];
    }
}
