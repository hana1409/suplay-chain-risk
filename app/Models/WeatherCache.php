<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherCache extends Model
{
    protected $fillable = [

        'country_id',

        'temperature',

        'rainfall',

        'wind_speed',

        'humidity',

        'weather_condition',

        'storm_risk',

        'weather_date'

    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}