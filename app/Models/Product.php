<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public const ALLOWED_DESCRIPTION = [
        'Ships in one business day',
        'Limited stock',
        'Customer favourite',
        'Eligible for free returns',
        'New this season',
    ];

    public const UPDATED_AT = null;

    protected $table = 'products';

    protected $fillable = [
        'brand_id',
        'category_id',
        'compare_at_price',
        'created_at',
        'description',
        'is_active',
        'name',
        'price',
        'sku',
        'slug',
    ];

    protected $casts = [
        'brand_id' => 'integer',
        'category_id' => 'integer',
        'compare_at_price' => 'decimal:2',
        'created_at' => 'datetime',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function productAttributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_id');
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'product_id');
    }

    public function productInventories(): HasMany
    {
        return $this->hasMany(ProductInventory::class, 'product_id');
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class, 'product_id');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items', 'product_id', 'order_id')
            ->withPivot('quantity', 'unit_price', 'discount_amount', 'tax_amount');
    }
}
