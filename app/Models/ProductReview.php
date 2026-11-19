<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    public const ALLOWED_COMMENT = [
        'Fast delivery and well packed.',
        'Quality is fine for the price.',
        'Sizing runs small.',
        'Stopped working after a month.',
        'Looks just like the photos.',
        'Instructions were unclear.',
        'Bought a second one as a gift.',
        'Returned it for a different colour.',
    ];

    public const ALLOWED_TITLE = [
        'Does the job',
        'Better than expected',
        'Not for me',
        'Great value',
        'Would buy again',
        'Arrived damaged',
        'Smaller than it looks',
        'Exactly as described',
    ];

    public const UPDATED_AT = null;

    protected $table = 'product_reviews';

    protected $fillable = [
        'comment',
        'created_at',
        'product_id',
        'rating',
        'title',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'product_id' => 'integer',
        'rating' => 'integer',
        'user_id' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
