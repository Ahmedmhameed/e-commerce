<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxRate extends Model
{
    public $timestamps = false;

    protected $table = 'tax_rates';

    protected $fillable = [
        'country_id',
        'rate',
        'state_province',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'rate' => 'decimal:4',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
