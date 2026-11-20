<?php

namespace App\Analytics\Examples;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class JoinSub
{
    public function run(): Collection
    {
        $reviewCounts = DB::table('product_reviews')
            ->select('product_id')
            ->selectRaw('COUNT(*) AS review_count')
            ->groupBy('product_id');

        return DB::table('products')
            ->joinSub($reviewCounts, 'reviews', 'reviews.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', 'reviews.review_count')
            ->orderBy('products.id')
            ->get();
    }
}
