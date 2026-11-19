<?php

namespace App\Analytics;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class HighValueCustomers
{
    public function run(): Collection
    {
        return $this->aboveAverage(CustomerOrders::eloquent());
    }

    public function runWithQueryBuilder(): Collection
    {
        return $this->aboveAverage(CustomerOrders::grouped());
    }

    private function aboveAverage(Builder $customers): Collection
    {
        $average = DB::query()->fromSub(clone $customers, 'customer_spend')->where('order_count', '>', 0)->selectRaw('AVG(total_spend)');

        // SQL concatenation presents the full name without changing the grouping.
        return DB::query()->fromSub($customers, 'customers')
            ->selectRaw("CONCAT(first_name, ' ', last_name) AS name")
            ->addSelect('order_count', 'total_spend', 'last_order_total')
            ->where('order_count', '>=', 3)
            ->where('total_spend', '>', $average)
            ->orderByDesc('total_spend')->orderBy('id')->get();
    }
}
