<?php

namespace App\Analytics;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

final class ProductRevenue
{
    public static function grouped(): Builder
    {
        // Subtract the returned units' proportional share of line revenue, including the integer line discount.
        // PostgreSQL numeric arithmetic preserves fractional cents until after aggregation.
        return OrderItem::query()
            ->leftJoin('order_returns as returns', function ($join): void {
                $join->on('returns.order_item_id', '=', 'order_items.id')->whereIn('returns.status', ['approved', 'refunded']);
            })
            ->whereIn('order_items.order_id', Order::successful()->select('id'))
            ->groupBy('order_items.product_id')->select('order_items.product_id')
            ->selectRaw('SUM((order_items.unit_price * order_items.quantity - order_items.discount_amount) * (order_items.quantity - COALESCE(returns.quantity, 0)) / order_items.quantity) AS revenue');
    }

    public static function ranked(string $windowExpression): Builder
    {
        return Product::query()
            ->joinSub(self::grouped(), 'sales', 'sales.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('sales.product_id', 'products.name as product', 'categories.id as category_id', 'categories.name as category', 'sales.revenue')
            ->selectRaw($windowExpression);
    }
}
