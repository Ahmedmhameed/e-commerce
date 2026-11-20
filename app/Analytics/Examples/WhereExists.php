<?php

namespace App\Analytics\Examples;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WhereExists
{
    public function run(): Collection
    {
        $highRatings = DB::table('product_reviews')
            ->select('product_reviews.id')
            ->whereColumn('product_reviews.product_id', 'products.id')
            ->where('rating', 5);

        return DB::table('products')
            ->whereExists($highRatings)
            ->orderBy('id')
            ->pluck('name', 'id');
    }
}
