<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    protected $fillable = [
        'country_id',
        'weather_score',
        'inflation_score',
        'currency_score',
        'news_score',
        'total_score',
        'risk_level',
        'calculated_at',
    ];

    protected $casts = [
        'weather_score'   => 'float',
        'inflation_score' => 'float',
        'currency_score'  => 'float',
        'news_score'      => 'float',
        'total_score'     => 'float',
        'calculated_at'   => 'datetime',
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

    /**
     * Returns a hex color for the risk level.
     */
    public function getRiskColorAttribute(): string
    {
        return match ($this->risk_level) {
            'Low'      => '#10B981',
            'Medium'   => '#F59E0B',
            'High'     => '#F97316',
            'Critical' => '#EF4444',
            default    => '#6B7280',
        };
    }

    /**
     * Returns a Bootstrap-compatible badge class name.
     */
    public function getRiskBadgeClassAttribute(): string
    {
        return match ($this->risk_level) {
            'Low'      => 'risk-low',
            'Medium'   => 'risk-medium',
            'High'     => 'risk-high',
            'Critical' => 'risk-critical',
            default    => 'risk-unknown',
        };
    }

    /**
     * Derive risk level from a numeric score.
     */
    public static function levelFromScore(float $score): string
    {
        if ($score < 30)  return 'Low';
        if ($score < 60)  return 'Medium';
        if ($score < 80)  return 'High';
        return 'Critical';
    }
}
