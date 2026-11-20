<?php

namespace App\Analytics;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class CustomerOrders
{
    public static function eloquent(): Builder
    {
        // Order spend uses the stored total, including shipping and tax.
        return User::query()
            ->select('users.id', 'users.first_name', 'users.last_name')
            ->withCount(['orders as order_count' => fn ($orders) => $orders->successful()])
            ->withSum(['orders as total_spend' => fn ($orders) => $orders->successful()], 'total')
            ->withMax('latestSuccessfulOrder as last_order_date', 'created_at')
            ->withMax('latestSuccessfulOrder as last_order_total', 'total');
    }
}
