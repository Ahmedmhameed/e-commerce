<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const ALLOWED_STATUS = [
        'completed',
        'failed',
        'refunded',
    ];

    public $timestamps = false;

    protected $table = 'payments';

    protected $fillable = [
        'amount',
        'order_id',
        'payment_method_id',
        'processed_at',
        'status',
        'transaction_reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'order_id' => 'integer',
        'payment_method_id' => 'integer',
        'processed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
