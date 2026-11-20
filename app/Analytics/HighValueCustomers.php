<?php

namespace App\Analytics;

use App\Models\User;
use Illuminate\Support\Collection;

final class HighValueCustomers
{
    public function run(): Collection
    {
        // The window averages all buyers before the three-order threshold is applied.
        $buyers = User::query()->fromSub(CustomerOrders::eloquent(), 'customers')
            ->select('customers.*')->where('order_count', '>', 0)
            ->selectRaw('AVG(total_spend) OVER () AS average_spend');

        return User::query()->fromSub($buyers, 'buyers')
            ->selectRaw("CONCAT(first_name, ' ', last_name) AS name")
            ->addSelect('order_count', 'total_spend', 'last_order_total')
            ->where('order_count', '>=', 3)
            ->whereColumn('total_spend', '>', 'average_spend')
            ->orderByDesc('total_spend')->orderBy('id')->toBase()->get();
    }
}
