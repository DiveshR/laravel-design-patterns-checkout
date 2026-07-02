<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatternIdempotencyKey extends Model
{
    protected $table = 'pattern_idempotency_keys';

    protected $fillable = [
        'user_id',
        'key',
        'request_hash',
        'status',
        'response_payload',
    ];

    protected $casts = [
        'response_payload' => 'array',
    ];
}
