<?php

namespace Database\Factories;

use App\Models\ProductInventory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductInventory>
 */
class ProductInventoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => ProductFactory::new(),
            'warehouse_id' => WarehouseFactory::new(),
            'quantity_on_hand' => fake()->numberBetween(0, 500),
            'quantity_reserved' => 0,
            'reorder_level' => fake()->numberBetween(5, 50),
        ];
    }
}
