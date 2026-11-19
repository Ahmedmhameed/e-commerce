<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscountCoupon extends Model
{
    public $timestamps = false;

    protected $table = 'discount_coupons';

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'ends_at',
        'is_active',
        'min_purchase_amount',
        'starts_at',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'min_purchase_amount' => 'integer',
        'starts_at' => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'coupon_id');
    }
}
