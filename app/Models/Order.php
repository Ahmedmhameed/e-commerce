<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'orders';

    protected $fillable = [
        'billing_address_id',
        'coupon_id',
        'created_at',
        'discount_amount',
        'order_status_id',
        'shipping_address_id',
        'shipping_cost',
        'shipping_method_id',
        'subtotal',
        'tax_amount',
        'total',
        'user_id',
    ];

    protected $casts = [
        'billing_address_id' => 'integer',
        'coupon_id' => 'integer',
        'created_at' => 'datetime',
        'discount_amount' => 'decimal:2',
        'order_status_id' => 'integer',
        'shipping_address_id' => 'integer',
        'shipping_cost' => 'decimal:2',
        'shipping_method_id' => 'integer',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'user_id' => 'integer',
    ];

    /**
     * Successful orders are paid, shipped, or delivered and have a completed payment.
     *
     * @param  Builder<Order>  $query
     * @return Builder<Order>
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query
            ->whereHas('status', fn (Builder $status) => $status->whereIn('status_name', ['paid', 'shipped', 'delivered']))
            ->whereHas('payment', fn (Builder $payment) => $payment->where('status', 'completed'));
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(DiscountCoupon::class, 'coupon_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id');
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class, 'order_id');
    }
}
