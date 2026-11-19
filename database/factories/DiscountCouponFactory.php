<?php

namespace Database\Factories;

use App\Models\DiscountCoupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiscountCoupon>
 */
class DiscountCouponFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->regexify('[A-Z0-9]{9}'),
            'discount_type' => 'percentage',
            'discount_value' => fake()->randomFloat(2, 5, 30),
            'ends_at' => now()->addMonth(),
            'is_active' => true,
            'min_purchase_amount' => fake()->numberBetween(0, 30),
            'starts_at' => now()->subDay(),
        ];
    }
}
