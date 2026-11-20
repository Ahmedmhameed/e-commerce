<?php

namespace App\Analytics;

use App\Models\User;
use Illuminate\Support\Collection;

final class ReturnBehavior
{
    public function run(): Collection
    {
        $counts = User::query()->select('id', 'first_name', 'last_name')
            ->withCount([
                'orders as total_orders' => fn ($orders) => $orders->successful(),
                'orders as orders_with_returns' => fn ($orders) => $orders->successful()
                    ->whereHas('orderItems.orderReturn', fn ($returns) => $returns->whereIn('status', ['approved', 'refunded'])),
            ]);

        return User::query()->fromSub($counts, 'customers')
            ->selectRaw("CONCAT(first_name, ' ', last_name) AS name")
            ->addSelect('total_orders', 'orders_with_returns')
            ->selectRaw('ROUND(100.0 * orders_with_returns / total_orders, 2) AS return_rate')
            ->where('orders_with_returns', '>', 1)
            ->orderByDesc('return_rate')->orderBy('id')->toBase()->get();
    }
}
