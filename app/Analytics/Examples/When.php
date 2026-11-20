<?php

namespace App\Analytics\Examples;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class When
{
    public function run(?int $categoryId = null): Collection
    {
        return DB::table('products')
            ->where('is_active', true)
            ->when($categoryId !== null, fn (Builder $query) => $query->where('category_id', $categoryId))
            ->orderBy('id')
            ->pluck('name', 'id');
    }
}
