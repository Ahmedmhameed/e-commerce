<?php

namespace App\Analytics\Examples;

use App\Models\Product;
use App\Models\ProductReview;

class OfMany
{
    public function run(int $productId): ?ProductReview
    {
        return Product::query()
            ->findOrFail($productId)
            ->productReviews()
            ->one()
            ->ofMany(['rating' => 'max', 'id' => 'max'])
            ->first();
    }
}
