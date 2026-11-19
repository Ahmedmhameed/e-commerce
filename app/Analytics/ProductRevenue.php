<?php

namespace App\Analytics;

use App\Models\Order;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final class ProductRevenue
{
    public static function grouped(): Builder
    {
        // Subtract the returned units' proportional share of line revenue, including the integer line discount.
        // PostgreSQL numeric arithmetic preserves fractional cents until after aggregation.
        return DB::table('order_items as items')
            ->leftJoin('order_returns as returns', function ($join): void {
                $join->on('returns.order_item_id', '=', 'items.id')->whereIn('returns.status', ['approved', 'refunded']);
            })
            ->whereIn('items.order_id', Order::successful()->select('id'))
            ->groupBy('items.product_id')->select('items.product_id')
            ->selectRaw('SUM((items.unit_price * items.quantity - items.discount_amount) * (items.quantity - COALESCE(returns.quantity, 0)) / items.quantity) AS revenue');
    }

    public static function ranked(string $windowExpression): Builder
    {
        return DB::query()->fromSub(self::grouped(), 'sales')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('sales.product_id', 'products.name as product', 'categories.id as category_id', 'categories.name as category', 'sales.revenue')
            ->selectRaw($windowExpression);
    }
}
