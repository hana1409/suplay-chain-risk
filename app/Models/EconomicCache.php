<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EconomicCache extends Model
{
    protected $fillable = [
        'country_id',
        'gdp',
        'inflation',
        'exports',
        'imports',
        'population',
        'year',
    ];

    protected $casts = [
        'gdp'       => 'float',
        'inflation' => 'float',
        'exports'   => 'float',
        'imports'   => 'float',
    ];

    // =====================
    // RELATIONSHIPS
    // =====================

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // =====================
    // ACCESSORS
    // =====================

    public function getFormattedGdpAttribute(): string
    {
        if (!$this->gdp) return 'N/A';

        if ($this->gdp >= 1_000_000_000_000) {
            return '$' . round($this->gdp / 1_000_000_000_000, 2) . 'T';
        }
        if ($this->gdp >= 1_000_000_000) {
            return '$' . round($this->gdp / 1_000_000_000, 2) . 'B';
        }
        return '$' . number_format($this->gdp, 0);
    }
}