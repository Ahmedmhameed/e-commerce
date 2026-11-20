<?php

namespace App\Analytics\Examples;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class WithAvg
{
    public function run(): Collection
    {
        return Product::query()
            ->select('id', 'name')
            ->withAvg('productReviews as average_rating', 'rating')
            ->orderBy('id')
            ->get();
    }
}
