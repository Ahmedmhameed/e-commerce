<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wishlist extends Model
{
    public const ALLOWED_NAME = [
        'Wish list',
        'Birthday',
        'Holiday gifts',
        'Home office',
        'Save for later',
    ];

    public const UPDATED_AT = null;

    protected $table = 'wishlists';

    protected $fillable = [
        'created_at',
        'name',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class, 'wishlist_id');
    }
}
