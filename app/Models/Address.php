<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    public $timestamps = false;

    protected $table = 'addresses';

    protected $fillable = [
        'city',
        'country_id',
        'extended_address',
        'is_default_billing',
        'is_default_shipping',
        'postal_code',
        'state_province',
        'street_address',
        'user_id',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'is_default_billing' => 'boolean',
        'is_default_shipping' => 'boolean',
        'user_id' => 'integer',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function billingOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'billing_address_id');
    }

    public function shippingOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }
}
