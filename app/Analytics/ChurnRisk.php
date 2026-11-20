<?php

namespace App\Analytics;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class ChurnRisk
{
    private const CUTOFF = "(SELECT MAX(created_at) - INTERVAL '90 days' FROM orders)";

    public function run(): Collection
    {
        $customers = CustomerOrders::eloquent()->whereDoesntHave(
            'orders',
            fn ($orders) => $orders->successful()->where('created_at', '>=', DB::raw(self::CUTOFF)),
        );

        return $this->inactive($customers->toBase())->get();
    }

    public function runWithQueryBuilder(): Collection
    {
        return $this->inactive(CustomerOrders::grouped())
            ->whereRaw('last_order_date < '.self::CUTOFF)
            ->get();
    }

    private function inactive(Builder $customers): Builder
    {
        // The database-wide anchor includes unsuccessful orders. The cutoff day is included in the 90-day window.
        return DB::query()->fromSub($customers, 'customers')
            ->selectRaw("CONCAT(first_name, ' ', last_name) AS name")
            ->addSelect('last_order_date', 'order_count', 'total_spend')
            ->where('order_count', '>=', 2)
            ->orderBy('last_order_date')->orderBy('id');
    }
}
