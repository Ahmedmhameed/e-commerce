<?php

namespace App\Analytics;

use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class MonthlyRevenueGrowth
{
    public function run(): Collection
    {
        $orders = Order::successful()->select('created_at', 'total')->toBase();

        // A PostgreSQL series fills the full order-history calendar, and LAG compares adjacent months.
        $rows = DB::select(<<<SQL
            WITH monthly_sales AS (
                SELECT DATE_TRUNC('month', created_at) AS month, SUM(total) AS revenue
                FROM ({$orders->toSql()}) AS successful_orders
                GROUP BY 1
            ), calendar AS (
                SELECT GENERATE_SERIES(DATE_TRUNC('month', MIN(created_at)), DATE_TRUNC('month', MAX(created_at)), INTERVAL '1 month') AS month
                FROM orders
            ), comparisons AS (
                SELECT calendar.month, COALESCE(monthly_sales.revenue, 0) AS revenue,
                    LAG(COALESCE(monthly_sales.revenue, 0)) OVER (ORDER BY calendar.month) AS previous_revenue
                FROM calendar LEFT JOIN monthly_sales USING (month)
            )
            SELECT TO_CHAR(month, 'YYYY-MM') AS month, revenue, previous_revenue,
                ROUND(100 * (revenue - previous_revenue) / NULLIF(previous_revenue, 0), 2) AS growth_percentage
            FROM comparisons ORDER BY month
            SQL, $orders->getBindings());

        return collect($rows);
    }
}
