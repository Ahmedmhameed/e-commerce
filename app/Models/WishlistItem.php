<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends Model
{
    public $timestamps = false;

    protected $table = 'wishlist_items';

    protected $fillable = [
        'added_at',
        'product_id',
        'wishlist_id',
    ];

    protected $casts = [
        'added_at' => 'datetime',
        'product_id' => 'integer',
        'wishlist_id' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function wishlist(): BelongsTo
    {
        return $this->belongsTo(Wishlist::class, 'wishlist_id');
    }
}
