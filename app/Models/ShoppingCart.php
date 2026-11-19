<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoppingCart extends Model
{
    protected $table = 'shopping_carts';

    protected $fillable = [
        'created_at',
        'updated_at',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
}
