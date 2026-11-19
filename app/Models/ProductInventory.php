<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductInventory extends Model
{
    public $timestamps = false;

    protected $table = 'product_inventories';

    protected $fillable = [
        'product_id',
        'quantity_on_hand',
        'quantity_reserved',
        'reorder_level',
        'warehouse_id',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'quantity_on_hand' => 'integer',
        'quantity_reserved' => 'integer',
        'reorder_level' => 'integer',
        'warehouse_id' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
