<?php

namespace App\Analytics\Examples;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class WithAggregate
{
    public function run(): Collection
    {
        return Product::query()
            ->select('id', 'name')
            ->withAggregate('productReviews as highest_rating', 'rating', 'max')
            ->orderBy('id')
            ->get();
    }
}
