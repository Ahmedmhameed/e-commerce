<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public const ALLOWED_ACTION = [
        'insert',
        'update',
        'delete',
    ];

    public const ALLOWED_TABLE_AFFECTED = [
        'orders',
        'products',
        'users',
        'payments',
        'shipments',
    ];

    public $timestamps = false;

    protected $table = 'audit_logs';

    protected $fillable = [
        'action',
        'performed_at',
        'record_id',
        'table_affected',
        'user_id',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'record_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
