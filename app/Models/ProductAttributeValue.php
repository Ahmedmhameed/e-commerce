<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValue extends Model
{
    public $timestamps = false;

    protected $table = 'product_attribute_values';

    protected $fillable = [
        'attribute_id',
        'product_id',
        'value',
    ];

    protected $casts = [
        'attribute_id' => 'integer',
        'product_id' => 'integer',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
