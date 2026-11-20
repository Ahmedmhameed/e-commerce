<?php

namespace App\Analytics;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;

final class PopularButPoorlyRated
{
    public function run(): Collection
    {
        $products = Product::query()->select('products.id', 'products.name as product')
            ->withAvg('productReviews as average_rating', 'rating')
            ->withCount('productReviews as review_count')
            ->withSum([
                'orderItems as units_sold' => fn ($items) => $items->whereIn('order_id', Order::successful()->select('id')),
            ], 'quantity');

        // The window gives every reviewed product equal weight before applying the sales threshold.
        $benchmarked = Product::query()->fromSub($products, 'metrics')->select('metrics.*')
            ->selectRaw('AVG(average_rating) OVER () AS rating_benchmark');

        return Product::query()->fromSub($benchmarked, 'benchmarked')
            ->select('product', 'units_sold', 'review_count')
            ->selectRaw('ROUND(average_rating, 2) AS average_rating')
            ->where('units_sold', '>=', 10)
            ->whereNotNull('average_rating')
            ->whereColumn('average_rating', '<', 'rating_benchmark')
            ->orderByDesc('units_sold')->orderBy('id')->toBase()->get();
    }
}
