<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'city' => mb_substr(fake()->city(), 0, 40),
            'country_id' => CountryFactory::new(),
            'extended_address' => null,
            'is_default_billing' => false,
            'is_default_shipping' => false,
            'postal_code' => fake()->numerify('#####'),
            'state_province' => fake()->stateAbbr(),
            'street_address' => mb_substr(fake()->streetAddress(), 0, 101),
            'user_id' => UserFactory::new(),
        ];
    }
}
