<?php

namespace App\Analytics;

use App\Models\Product;
use Illuminate\Support\Collection;

final class BestProductPerCategory
{
    public function run(): Collection
    {
        // DENSE_RANK preserves all ties at the maximum revenue.
        $sales = ProductRevenue::ranked('DENSE_RANK() OVER (PARTITION BY categories.id ORDER BY sales.revenue DESC) AS revenue_rank');

        return Product::query()->fromSub($sales, 'sales')->select('category', 'product')->selectRaw('ROUND(revenue, 2) AS revenue')
            ->where('revenue_rank', 1)->orderBy('category')->orderBy('product')->orderBy('product_id')->toBase()->get();
    }
}
