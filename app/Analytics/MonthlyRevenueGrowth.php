<?php

namespace App\Analytics;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class MonthlyRevenueGrowth
{
    public function run(): Collection
    {
        $calendar = $this->calendar();
        $sales = $this->monthlySales();

        $months = Order::query()->fromSub($calendar, 'calendar')
            ->leftJoinSub($sales, 'sales', 'sales.month', '=', 'calendar.month')
            ->select('calendar.month')
            ->selectRaw('COALESCE(sales.revenue, 0) AS revenue');

        // LAG reads the preceding calendar month, including months with no sales.
        $comparisons = Order::query()->fromSub($months, 'months')->select('month', 'revenue')
            ->selectRaw('LAG(revenue) OVER (ORDER BY month) AS previous_revenue');

        return Order::query()->fromSub($comparisons, 'comparisons')
            ->selectRaw("TO_CHAR(month, 'YYYY-MM') AS month")
            ->addSelect('revenue', 'previous_revenue')
            ->selectRaw('ROUND(100 * (revenue - previous_revenue) / NULLIF(previous_revenue, 0), 2) AS growth_percentage')
            ->orderBy('comparisons.month')->toBase()->get();
    }

    private function calendar(): Builder
    {
        // GENERATE_SERIES produces every month between the first and last order, across all statuses.
        return Order::query()->selectRaw(
            "GENERATE_SERIES(DATE_TRUNC('month', MIN(created_at)), DATE_TRUNC('month', MAX(created_at)), INTERVAL '1 month') AS month"
        );
    }

    private function monthlySales(): Builder
    {
        // DATE_TRUNC groups successful orders by both year and month.
        return Order::successful()->selectRaw("DATE_TRUNC('month', created_at) AS month, SUM(total) AS revenue")
            ->groupBy('month');
    }
}
