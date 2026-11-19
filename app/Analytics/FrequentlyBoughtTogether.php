<?php

namespace App\Analytics;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Rank unordered pairs by descending order count, then ascending first and second product names.
 */
final class FrequentlyBoughtTogether
{
    public function run(): Collection
    {
        $items = OrderItem::query()->select('order_id', 'product_id')
            ->whereIn('order_id', Order::successful()->select('id'))->distinct();

        // SQL self-join gives each unordered pair one orientation per order.
        return DB::query()->fromSub($items, 'left_item')
            ->joinSub(clone $items, 'right_item', function ($join): void {
                $join->on('left_item.order_id', '=', 'right_item.order_id')
                    ->on('left_item.product_id', '<', 'right_item.product_id');
            })
            ->join('products as first_product', 'first_product.id', '=', 'left_item.product_id')
            ->join('products as second_product', 'second_product.id', '=', 'right_item.product_id')
            ->select('first_product.name as product_one', 'second_product.name as product_two')
            ->selectRaw('COUNT(DISTINCT left_item.order_id) AS order_count')
            ->groupBy('first_product.id', 'second_product.id')
            ->orderByDesc('order_count')->orderBy('product_one')->orderBy('product_two')
            ->orderBy('first_product.id')->orderBy('second_product.id')->limit(5)->get();
    }
}
