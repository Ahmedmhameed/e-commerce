<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    public const ALLOWED_DESCRIPTION = [
        'Sold here since launch',
        'Exclusive to this store',
        'Ships from the brand',
        'Made in the USA',
        'Imported',
    ];

    public $timestamps = false;

    protected $table = 'brands';

    protected $fillable = [
        'description',
        'logo_url',
        'name',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
