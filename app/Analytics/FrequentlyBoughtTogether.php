<?php

namespace App\Analytics;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Rank unordered pairs by descending order count, then ascending first and second product names.
 */
final class FrequentlyBoughtTogether
{
    public function run(): Collection
    {
        // Joining a second line from the same order pairs products by increasing ID.
        return Product::query()->from('products as first_product')
            ->join('order_items as left_item', 'left_item.product_id', '=', 'first_product.id')
            ->join('order_items as right_item', function ($join): void {
                $join->on('right_item.order_id', '=', 'left_item.order_id')
                    ->on('right_item.product_id', '>', 'left_item.product_id');
            })
            ->join('products as second_product', 'second_product.id', '=', 'right_item.product_id')
            ->whereIn('left_item.order_id', Order::successful()->select('id'))
            ->select('first_product.name as product_one', 'second_product.name as product_two')
            ->selectRaw('COUNT(DISTINCT left_item.order_id) AS order_count')
            ->groupBy('first_product.id', 'second_product.id')
            ->orderByDesc('order_count')->orderBy('product_one')->orderBy('product_two')
            ->orderBy('first_product.id')->orderBy('second_product.id')->limit(5)->toBase()->get();
    }
}
