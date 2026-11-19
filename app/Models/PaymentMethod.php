<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    public const ALLOWED_NAME = [
        'credit_card',
        'debit_card',
        'digital_wallet',
        'gift_card',
        'bank_transfer',
    ];

    public $timestamps = false;

    protected $table = 'payment_methods';

    protected $fillable = [
        'name',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payment_method_id');
    }
}
