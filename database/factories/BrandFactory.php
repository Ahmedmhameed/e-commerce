<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Brand>
 */
class BrandFactory extends Factory
{
    public function definition(): array
    {
        return [
            'description' => fake()->randomElement(Brand::ALLOWED_DESCRIPTION),
            'logo_url' => 'https://example.test/'.fake()->lexify('????????'),
            'name' => mb_substr(fake()->company(), 0, 114),
        ];
    }
}
