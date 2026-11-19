<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    public const ALLOWED_ALT_TEXT = [
        'Front view',
        'Side view',
        'In use',
        'Packaging',
        'Close-up detail',
    ];

    public $timestamps = false;

    protected $table = 'product_images';

    protected $fillable = [
        'alt_text',
        'product_id',
        'sort_order',
        'url',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
