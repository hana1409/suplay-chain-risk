<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'country_code',
        'country_code3',
        'country_name',
        'official_name',
        'capital',
        'region',
        'subregion',
        'currency_code',
        'currency_name',
        'language',
        'population',
        'latitude',
        'longitude',
        'timezone',
        'flag',
        'flag_png',
        'flag_svg',
    ];

    // =====================
    // RELATIONSHIPS
    // =====================

    public function riskScore()
    {
        return $this->hasOne(RiskScore::class);
    }

    public function economicCache()
    {
        return $this->hasOne(EconomicCache::class)->latestOfMany();
    }

    public function economicCaches()
    {
        return $this->hasMany(EconomicCache::class)->orderByDesc('year');
    }

    public function weatherCache()
    {
        return $this->hasOne(WeatherCache::class)->latestOfMany();
    }

    public function currencyCache()
    {
        return $this->hasOne(CurrencyCache::class)->latestOfMany();
    }

    public function ports()
    {
        return $this->hasMany(Port::class);
    }

    public function newsCaches()
    {
        return $this->hasMany(NewsCache::class)->orderByDesc('published_at');
    }

    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    // =====================
    // ACCESSORS
    // =====================

    public function getFlagUrlAttribute(): string
    {
        if ($this->flag_png) {
            return $this->flag_png;
        }
        return 'https://flagcdn.com/w80/' . strtolower($this->country_code) . '.png';
    }

    public function getFormattedPopulationAttribute(): string
    {
        if (!$this->population) return 'N/A';
        return number_format($this->population);
    }
}