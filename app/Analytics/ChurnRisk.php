<?php

namespace App\Analytics;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class ChurnRisk
{
    private function cutoff(): Builder
    {
        // Anchor the interval to the latest order of any status in this same statement.
        return Order::query()->selectRaw("created_at - INTERVAL '90 days'")
            ->whereNotNull('created_at')->latest()->limit(1);
    }

    public function run(): Collection
    {
        $customers = CustomerOrders::eloquent()->whereDoesntHave(
            'orders',
            fn ($orders) => $orders->successful()->where('created_at', '>=', $this->cutoff()),
        );

        return User::query()->fromSub($customers, 'customers')
            ->selectRaw("CONCAT(first_name, ' ', last_name) AS name")
            ->addSelect('last_order_date', 'order_count', 'total_spend')
            ->where('order_count', '>=', 2)
            ->orderBy('last_order_date')->orderBy('id')->toBase()->get();
    }
}
