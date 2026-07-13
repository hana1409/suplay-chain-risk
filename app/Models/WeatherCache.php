<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherCache extends Model
{
    protected $fillable = [
        'country_id',
        'temperature',
        'rainfall',
        'rain',
        'wind_speed',
        'humidity',
        'weather_condition',
        'weather_code',
        'weather_desc',
        'storm_risk',
        'weather_date',
    ];

    protected $casts = [
        'temperature'  => 'float',
        'rainfall'     => 'float',
        'rain'         => 'float',
        'wind_speed'   => 'float',
        'humidity'     => 'integer',
        'storm_risk'   => 'integer',
        'weather_date' => 'datetime',
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

    public function getWeatherIconAttribute(): string
    {
        $code = $this->weather_code ?? 0;
        $condition = strtolower($this->weather_condition ?? '');

        if (str_contains($condition, 'storm') || str_contains($condition, 'thunder')) return '⛈️';
        if (str_contains($condition, 'snow')) return '❄️';
        if (str_contains($condition, 'rain') || str_contains($condition, 'drizzle')) return '🌧️';
        if (str_contains($condition, 'fog') || str_contains($condition, 'mist')) return '🌫️';
        if (str_contains($condition, 'cloud')) return '🌤️';
        if (str_contains($condition, 'clear') || str_contains($condition, 'sunny')) return '☀️';

        if ($code === 0)  return '☀️';
        if ($code <= 3)   return '🌤️';
        if ($code <= 48)  return '🌫️';
        if ($code <= 67)  return '🌧️';
        if ($code <= 77)  return '❄️';
        if ($code <= 82)  return '🌦️';
        if ($code <= 99)  return '⛈️';

        return '🌡️';
    }

    public function getWeatherRiskScoreAttribute(): float
    {
        $score = 0;

        $temp = abs($this->temperature ?? 25);
        if ($temp > 45 || $temp < -20) $score += 40;
        elseif ($temp > 38 || $temp < -5) $score += 20;

        $wind = $this->wind_speed ?? 0;
        if ($wind > 80) $score += 40;
        elseif ($wind > 50) $score += 20;

        $rain = $this->rainfall ?? $this->rain ?? 0;
        if ($rain > 50) $score += 20;
        elseif ($rain > 20) $score += 10;

        if ($this->storm_risk > 7) $score += 20;
        elseif ($this->storm_risk > 4) $score += 10;

        return min(100, $score);
    }
}