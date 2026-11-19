<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingMethod extends Model
{
    public const ALLOWED_CARRIER = [
        'UPS',
        'FedEx',
        'USPS',
        'DHL',
        'In store',
    ];

    public const ALLOWED_NAME = [
        'standard',
        'expedited',
        'two_day',
        'overnight',
        'store_pickup',
    ];

    public $timestamps = false;

    protected $table = 'shipping_methods';

    protected $fillable = [
        'base_cost',
        'carrier',
        'estimated_days',
        'name',
    ];

    protected $casts = [
        'base_cost' => 'decimal:2',
        'estimated_days' => 'integer',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'shipping_method_id');
    }
}
