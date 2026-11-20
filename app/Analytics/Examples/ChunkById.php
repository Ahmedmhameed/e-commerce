<?php

namespace App\Analytics\Examples;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ChunkById
{
    public function run(int $batchSize = 100): int
    {
        $activeCount = 0;

        Product::query()
            ->select('id')
            ->where('is_active', true)
            ->chunkById($batchSize, function (Collection $products) use (&$activeCount): void {
                $activeCount += $products->count();
            });

        return $activeCount;
    }
}
