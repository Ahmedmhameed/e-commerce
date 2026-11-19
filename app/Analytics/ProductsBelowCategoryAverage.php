<?php

namespace App\Analytics;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Include sold products with zero net revenue after returns in the category average.
 */
final class ProductsBelowCategoryAverage
{
    public function run(): Collection
    {
        // A window average keeps one row per sold product and ignores unsold products.
        $sales = ProductRevenue::ranked('AVG(sales.revenue) OVER (PARTITION BY categories.id) AS category_average');

        return DB::query()->fromSub($sales, 'sales')->select('product', 'category')
            ->selectRaw('ROUND(revenue, 2) AS revenue, ROUND(category_average, 2) AS category_average')
            ->whereColumn('revenue', '<', 'category_average')
            ->orderBy('category')->orderBy('product')->orderBy('product_id')->get();
    }
}
