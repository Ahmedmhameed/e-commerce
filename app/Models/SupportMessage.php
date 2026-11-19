<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    public const ALLOWED_MESSAGE = [
        'Thanks, that worked.',
        'Can you check the tracking number?',
        'I have issued a refund to your card.',
        'Please send a photo of the damage.',
        'Your replacement ships today.',
        'I still have not received it.',
    ];

    public const ALLOWED_SENDER_TYPE = [
        'customer',
        'agent',
    ];

    public $timestamps = false;

    protected $table = 'support_messages';

    protected $fillable = [
        'message',
        'sender_type',
        'sent_at',
        'ticket_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'ticket_id' => 'integer',
    ];

    public function supportTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }
}
