<?php

namespace Database\Factories;

use App\Models\WishlistItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WishlistItem>
 */
class WishlistItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'added_at' => now(),
            'product_id' => ProductFactory::new(),
            'wishlist_id' => WishlistFactory::new(),
        ];
    }
}
