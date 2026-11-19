<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    public $timestamps = false;

    protected $table = 'shipments';

    protected $fillable = [
        'delivered_at',
        'order_id',
        'shipped_at',
        'tracking_number',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'order_id' => 'integer',
        'shipped_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
