<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    public const ALLOWED_NAME = [
        'color',
        'finish',
        'accent_color',
        'trim_color',
    ];

    public $timestamps = false;

    protected $table = 'product_attributes';

    protected $fillable = [
        'name',
        'type',
    ];

    public function productAttributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }
}
