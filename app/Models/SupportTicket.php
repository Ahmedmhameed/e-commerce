<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    public const ALLOWED_PRIORITY = [
        'low',
        'medium',
        'high',
    ];

    public const ALLOWED_STATUS = [
        'open',
        'pending',
        'resolved',
        'closed',
    ];

    public const ALLOWED_SUBJECT = [
        'Where is my order?',
        'Item arrived damaged',
        'Change my shipping address',
        'Refund not received',
        'Coupon did not apply',
        'Cannot sign in',
    ];

    public const UPDATED_AT = null;

    protected $table = 'support_tickets';

    protected $fillable = [
        'created_at',
        'priority',
        'status',
        'subject',
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

    public function supportMessages(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'ticket_id');
    }
}
