<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\WeatherCache;
use App\Services\WeatherService;

class SyncWeather extends Command
{
    protected $signature = 'weather:sync';

    protected $description = 'Sync weather data from Open-Meteo API';

    public function handle(WeatherService $weatherService)
    {
        $countries = Country::all();

        foreach ($countries as $country) {

            if (!$country->latitude || !$country->longitude) {
                $this->warn("Skipped {$country->country_name} (No Coordinate)");
                continue;
            }

            try {

    $weather = $weatherService->getWeather(
        $country->latitude,
        $country->longitude
    );

    if (!$weather) {
        $this->warn("No weather data : {$country->country_name}");
        continue;
    }

} catch (\Exception $e) {

    $this->error("Failed : {$country->country_name}");

    continue;

}

            WeatherCache::updateOrCreate(

                [
                    'country_id' => $country->id
                ],

                [

                    'temperature' => $weather['temperature_2m'] ?? null,

                    'rainfall' => $weather['rain'] ?? 0,

                    'wind_speed' => $weather['wind_speed_10m'] ?? null,

                    'humidity' => $weather['relative_humidity_2m'] ?? null,

                    'weather_condition' => $this->weatherDescription(
                        $weather['weather_code'] ?? 0
                    ),

                    'storm_risk' => $this->stormRisk(
                        $weather['weather_code'] ?? 0
                    ),

                    'weather_date' => now()

                ]

            );

            $this->info("Synced : {$country->country_name}");
        }

        $this->info("================================");
        $this->info("Weather Sync Completed!");
        $this->info("================================");
    }

    private function stormRisk($code)
    {
        if (in_array($code,[95,96,99])) {
            return 100;
        }

        if (in_array($code,[80,81,82])) {
            return 70;
        }

        return 10;
    }

    private function weatherDescription($code)
    {
        return match($code){

            0 => 'Clear',

            1,2,3 => 'Cloudy',

            45,48 => 'Fog',

            51,53,55 => 'Drizzle',

            61,63,65 => 'Rain',

            71,73,75 => 'Snow',

            80,81,82 => 'Rain Shower',

            95,96,99 => 'Thunderstorm',

            default => 'Unknown'

        };
    }
}