<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatternOrder extends Model
{
    protected $fillable = [
        'user_id',
        'pattern_product_id',
        'quantity',
        'amount_in_paise',
        'status',
        'payment_provider',
        'provider_payment_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(PatternProduct::class, 'pattern_product_id');
    }
}
