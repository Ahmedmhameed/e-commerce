<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
    public const ALLOWED_DESCRIPTION = [
        'Waiting on the customer',
        'In the warehouse',
        'With the carrier',
        'Closed',
    ];

    public const ALLOWED_STATUS_NAME = [
        'pending',
        'paid',
        'shipped',
        'delivered',
        'cancelled',
        'refunded',
    ];

    public $timestamps = false;

    protected $table = 'order_statuses';

    protected $fillable = [
        'description',
        'status_name',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'order_status_id');
    }
}
