<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    public const ALLOWED_NAME = [
        'East Coast DC',
        'West Coast DC',
        'Midwest DC',
        'Southeast DC',
        'Texas DC',
        'Mountain DC',
    ];

    public $timestamps = false;

    protected $table = 'warehouses';

    protected $fillable = [
        'address',
        'city',
        'country_id',
        'name',
    ];

    protected $casts = [
        'country_id' => 'integer',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function productInventories(): HasMany
    {
        return $this->hasMany(ProductInventory::class, 'warehouse_id');
    }
}
