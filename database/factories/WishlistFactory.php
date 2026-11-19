<?php

namespace Database\Factories;

use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Wishlist>
 */
class WishlistFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_at' => now(),
            'name' => fake()->randomElement(Wishlist::ALLOWED_NAME),
            'user_id' => UserFactory::new(),
        ];
    }
}
