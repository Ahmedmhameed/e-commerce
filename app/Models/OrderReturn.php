<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    public const ALLOWED_STATUS = [
        'refunded',
        'approved',
        'requested',
        'rejected',
    ];

    public $timestamps = false;

    protected $table = 'order_returns';

    protected $fillable = [
        'order_item_id',
        'processed_at',
        'quantity',
        'reason_id',
        'requested_at',
        'status',
    ];

    protected $casts = [
        'order_item_id' => 'integer',
        'processed_at' => 'datetime',
        'quantity' => 'integer',
        'reason_id' => 'integer',
        'requested_at' => 'datetime',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(ReturnReason::class, 'reason_id');
    }
}
