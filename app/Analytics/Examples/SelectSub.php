<?php

namespace App\Analytics\Examples;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SelectSub
{
    public function run(): Collection
    {
        $latestRating = DB::table('product_reviews')
            ->select('rating')
            ->whereColumn('product_id', 'products.id')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(1);

        return DB::table('products')
            ->select('products.id', 'products.name')
            ->selectSub($latestRating, 'latest_rating')
            ->orderBy('products.id')
            ->get();
    }
}
