<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnReason extends Model
{
    public const ALLOWED_REASON_TEXT = [
        'Arrived damaged',
        'Wrong item sent',
        'Does not fit',
        'Not as described',
        'Changed my mind',
        'Arrived too late',
    ];

    public $timestamps = false;

    protected $table = 'return_reasons';

    protected $fillable = [
        'reason_text',
    ];

    public function orderReturns(): HasMany
    {
        return $this->hasMany(OrderReturn::class, 'reason_id');
    }
}
