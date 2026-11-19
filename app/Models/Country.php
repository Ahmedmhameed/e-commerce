<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    public $timestamps = false;

    protected $table = 'countries';

    protected $fillable = [
        'code',
        'name',
    ];

    public function taxRates(): HasMany
    {
        return $this->hasMany(TaxRate::class, 'country_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'country_id');
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class, 'country_id');
    }
}
