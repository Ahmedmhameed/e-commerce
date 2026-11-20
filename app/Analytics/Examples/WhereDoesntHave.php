<?php

namespace App\Analytics\Examples;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class WhereDoesntHave
{
    public function run(): Collection
    {
        return Product::query()
            ->whereDoesntHave('productReviews', fn (Builder $reviews) => $reviews->where('rating', '>=', 4))
            ->orderBy('id')
            ->get(['id', 'name']);
    }
}
