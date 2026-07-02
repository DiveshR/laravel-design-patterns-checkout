<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatternProduct extends Model
{
    protected $fillable = [
        'name',
        'stock',
        'price_in_paise',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(PatternOrder::class);
    }
}
