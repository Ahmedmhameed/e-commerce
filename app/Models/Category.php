<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    public const ALLOWED_DESCRIPTION = [
        'Free shipping over $35',
        'Bestsellers',
        'New arrivals weekly',
        'Clearance',
    ];

    public const ALLOWED_NAME = [
        'Books',
        'Movies',
        'Music',
        'Games',
        'Electronics',
        'Computers',
        'Home',
        'Garden',
        'Tools',
        'Grocery',
        'Health',
        'Beauty',
        'Toys',
        'Kids',
        'Baby',
        'Clothing',
        'Shoes',
        'Jewelry',
        'Sports',
        'Outdoors',
        'Automotive',
        'Office',
    ];

    public $timestamps = false;

    protected $table = 'categories';

    protected $fillable = [
        'description',
        'name',
        'parent_category_id',
        'slug',
    ];

    protected $casts = [
        'parent_category_id' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_category_id');
    }
}
