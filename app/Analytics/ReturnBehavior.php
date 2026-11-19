<?php

namespace App\Analytics;

use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class ReturnBehavior
{
    public function run(): Collection
    {
        $orders = Order::successful()->select('id', 'user_id')
            ->withExists(['orderItems as has_return' => fn ($items) => $items->whereHas(
                'orderReturn', fn ($returns) => $returns->whereIn('status', ['approved', 'refunded'])
            )]);

        // PostgreSQL FILTER counts each qualifying order once regardless of the number of returned lines.
        return DB::query()->fromSub($orders, 'successful_orders')
            ->join('users', 'users.id', '=', 'successful_orders.user_id')
            ->selectRaw("CONCAT(users.first_name, ' ', users.last_name) AS name")
            ->selectRaw('COUNT(*) AS total_orders, COUNT(*) FILTER (WHERE has_return) AS orders_with_returns')
            ->selectRaw('ROUND(100.0 * COUNT(*) FILTER (WHERE has_return) / COUNT(*), 2) AS return_rate')
            ->groupBy('users.id')->havingRaw('COUNT(*) FILTER (WHERE has_return) > 1')
            ->orderByDesc('return_rate')->orderBy('users.id')->get();
    }
}
