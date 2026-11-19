<?php

namespace App\Analytics;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class ChurnRisk
{
    public function run(): Collection
    {
        return $this->inactive(CustomerOrders::eloquent());
    }

    public function runWithQueryBuilder(): Collection
    {
        return $this->inactive(CustomerOrders::grouped());
    }

    private function inactive(Builder $customers): Collection
    {
        // The database-wide anchor includes unsuccessful orders. The cutoff day is included in the 90-day window.
        return DB::query()->fromSub($customers, 'customers')
            ->selectRaw("CONCAT(first_name, ' ', last_name) AS name")
            ->addSelect('last_order_date', 'order_count', 'total_spend')
            ->where('order_count', '>=', 2)
            ->whereRaw("last_order_date < (SELECT MAX(created_at) - INTERVAL '90 days' FROM orders)")
            ->orderBy('last_order_date')->orderBy('id')->get();
    }
}
