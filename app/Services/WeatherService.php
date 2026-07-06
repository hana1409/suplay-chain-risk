<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function getWeather($lat,$lon)
    {
    $response = Http::timeout(20)
    ->retry(3, 1000)
    ->get(
        'https://api.open-meteo.com/v1/forecast',
        [
            'latitude' => $lat,
            'longitude' => $lon,
            'current' => implode(',',[
                'temperature_2m',
                'relative_humidity_2m',
                'rain',
                'wind_speed_10m',
                'weather_code'
            ])
        ]
    );

        if(!$response->successful()){
            return null;
        }

        return $response->json()['current'];
    }
}