<?php

namespace App\Analytics;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final class CustomerOrders
{
    public static function eloquent(): Builder
    {
        // Order spend uses the stored total, including shipping and tax.
        return User::query()
            ->select('users.id', 'users.first_name', 'users.last_name')
            ->withCount(['orders as order_count' => fn ($orders) => $orders->successful()])
            ->withSum(['orders as total_spend' => fn ($orders) => $orders->successful()], 'total')
            ->addSelect([
                'last_order_date' => Order::successful()->select('created_at')->whereColumn('user_id', 'users.id')->latest('created_at')->latest('id')->limit(1),
                'last_order_total' => Order::successful()->select('total')->whereColumn('user_id', 'users.id')->latest('created_at')->latest('id')->limit(1),
            ])->toBase();
    }

    public static function grouped(): Builder
    {
        $orders = Order::successful()->select('id', 'user_id', 'created_at', 'total');

        // PostgreSQL array_agg ordering makes the latest order deterministic on date ties.
        $spend = DB::query()->fromSub($orders, 'successful_orders')
            ->select('user_id')
            ->selectRaw('COUNT(*) AS order_count, SUM(total) AS total_spend, MAX(created_at) AS last_order_date, (ARRAY_AGG(total ORDER BY created_at DESC, id DESC))[1] AS last_order_total')
            ->groupBy('user_id');

        return DB::table('users')->joinSub($spend, 'spend', 'spend.user_id', '=', 'users.id')
            ->select('users.id', 'first_name', 'last_name', 'order_count', 'total_spend', 'last_order_date', 'last_order_total');
    }
}
