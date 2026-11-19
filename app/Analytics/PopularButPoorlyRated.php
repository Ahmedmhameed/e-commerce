<?php

namespace App\Analytics;

use App\Models\Order;
use App\Models\ProductReview;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class PopularButPoorlyRated
{
    public function run(): Collection
    {
        // Separate aggregates avoid multiplying order lines by review rows.
        $sales = DB::table('order_items')->select('product_id')->selectRaw('SUM(order_items.quantity) AS units_sold')
            ->whereIn('order_id', Order::successful()->select('id'))
            ->groupBy('product_id');

        $ratings = ProductReview::query()->select('product_id')
            ->selectRaw('AVG(rating) AS average_rating, COUNT(*) AS review_count')->groupBy('product_id');

        $average = DB::query()->fromSub(clone $ratings, 'product_ratings')->selectRaw('AVG(average_rating)');

        return DB::table('products')->joinSub($sales, 'sales', 'sales.product_id', '=', 'products.id')
            ->joinSub($ratings, 'ratings', 'ratings.product_id', '=', 'products.id')
            ->select('products.name as product', 'units_sold', 'review_count')
            ->selectRaw('ROUND(average_rating, 2) AS average_rating')
            ->where('sales.units_sold', '>=', 10)
            ->where('average_rating', '<', $average)->orderByDesc('units_sold')->orderBy('products.id')->get();
    }
}
