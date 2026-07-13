<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyCache extends Model
{
    protected $fillable = [
        'country_id',
        'base_currency',
        'target_currency',
        'exchange_rate',
        'rate_change_pct',
        'rate_date',
    ];

    protected $casts = [
        'exchange_rate'   => 'float',
        'rate_change_pct' => 'float',
        'rate_date'       => 'datetime',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}