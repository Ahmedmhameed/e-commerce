<?php

namespace App\Analytics\Examples;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class WhereRelation
{
    public function run(): Collection
    {
        return Product::query()
            ->whereRelation('productReviews', 'rating', '<=', 2)
            ->orderBy('id')
            ->get(['id', 'name']);
    }
}
