<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $table = 'order_items';

    protected $fillable = [
        'discount_amount',
        'order_id',
        'product_id',
        'quantity',
        'tax_amount',
        'unit_price',
    ];

    protected $casts = [
        'discount_amount' => 'integer',
        'order_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'tax_amount' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function orderReturn(): HasOne
    {
        return $this->hasOne(OrderReturn::class, 'order_item_id');
    }
}
