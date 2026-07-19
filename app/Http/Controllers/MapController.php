<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\WeatherCache;
use App\Models\Watchlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MapController extends Controller
{
    // =====================================================
    // MAIN VIEW
    // =====================================================

    public function index()
    {
        // Pass only port count — actual port data loaded via AJAX
        $portCount = Port::whereNotNull('latitude')->whereNotNull('longitude')->count();

        return view('map.index', compact('portCount'));
    }

    // =====================================================
    // AJAX: Countries with weather + risk (for map markers)
    // =====================================================

    public function countriesData()
    {
        $countries = Country::with(['riskScore', 'weatherCache'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'country_code', 'country_name', 'latitude', 'longitude', 'flag', 'region']);

        $data = $countries->map(function ($c) {
            $risk    = $c->riskScore;
            $weather = $c->weatherCache;

            $level = $risk?->risk_level ?? 'Unknown';
            $score = $risk?->total_score ?? 0;

            $color = match ($level) {
                'Low'      => '#10B981',
                'Medium'   => '#F59E0B',
                'High'     => '#F97316',
                'Critical' => '#EF4444',
                default    => '#374151',
            };

            $condition = $weather?->weather_condition ?? 'Unknown';
            $weatherIcon = $this->conditionToIcon($condition, $weather?->wind_speed ?? 0);

            return [
                'code'            => $c->country_code,
                'name'            => $c->country_name,
                'lat'             => (float) $c->latitude,
                'lng'             => (float) $c->longitude,
                'flag'            => $c->flag,
                'region'          => $c->region,
                'score'           => $score,
                'level'           => $level,
                'color'           => $color,
                // Weather
                'weather_icon'    => $weatherIcon,
                'weather_condition' => $condition,
                'temperature'     => $weather?->temperature !== null ? round($weather->temperature, 1) . '°C' : 'N/A',
                'wind_speed'      => $weather?->wind_speed   !== null ? round($weather->wind_speed, 1) . ' km/h' : 'N/A',
                'rainfall'        => $weather ? round(max($weather->rainfall ?? 0, $weather->rain ?? 0), 1) . ' mm' : 'N/A',
            ];
        });

        return response()->json($data);
    }

    // =====================================================
    // AJAX: Ports data (paginated / all)
    // =====================================================

    public function portsData()
    {
        $ports = Port::with('country')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get([
                'id', 'country_id', 'port_name', 'city',
                'port_type', 'latitude', 'longitude', 'status',
            ]);

        $data = $ports->map(fn($p) => [
            'id'        => $p->id,
            'name'      => $p->port_name,
            'city'      => $p->city ?: 'N/A',
            'country'   => $p->country?->country_name ?? 'N/A',
            'type'      => $p->port_type ?: 'N/A',
            'lat'       => (float) $p->latitude,
            'lng'       => (float) $p->longitude,
            'status'    => $p->status,
        ]);

        return response()->json($data);
    }

    // =====================================================
    // AJAX: Port weather (fetched from Open-Meteo by lat/lng)
    // =====================================================

    public function portWeather(int $portId)
    {
        $port = Port::findOrFail($portId);

        $cacheKey = "port_weather_{$portId}";

        $weather = Cache::remember($cacheKey, 1800, function () use ($port) {
            try {
                $response = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude'  => $port->latitude,
                    'longitude' => $port->longitude,
                    'current'   => 'temperature_2m,relative_humidity_2m,rain,wind_speed_10m,weather_code',
                ]);

                if (!$response->successful()) return null;

                $current = $response->json()['current'] ?? null;
                if (!$current) return null;

                $code      = $current['weather_code'] ?? 0;
                $condition = $this->codeToCondition($code);
                $windSpeed = $current['wind_speed_10m'] ?? 0;

                return [
                    'condition'   => $condition,
                    'icon'        => $this->conditionToIcon($condition, $windSpeed),
                    'temperature' => round($current['temperature_2m'] ?? 0, 1) . '°C',
                    'wind_speed'  => round($windSpeed, 1) . ' km/h',
                    'rainfall'    => round($current['rain'] ?? 0, 1) . ' mm',
                    'humidity'    => ($current['relative_humidity_2m'] ?? 'N/A') . '%',
                ];
            } catch (\Throwable) {
                return null;
            }
        });

        if (!$weather) {
            $weather = [
                'condition'   => 'N/A',
                'icon'        => 'partly-cloudy',
                'temperature' => 'N/A',
                'wind_speed'  => 'N/A',
                'rainfall'    => 'N/A',
                'humidity'    => 'N/A',
            ];
        }

        return response()->json(array_merge([
            'port_name' => $port->port_name,
            'city'      => $port->city ?: 'N/A',
            'country'   => $port->country?->country_name ?? 'N/A',
            'type'      => $port->port_type ?: 'N/A',
            'lat'       => round($port->latitude, 4),
            'lng'       => round($port->longitude, 4),
        ], $weather));
    }

    // =====================================================
    // AJAX: Country popup data (existing — unchanged)
    // =====================================================

    public function countryPopup(string $code)
    {
        $country = Country::where('country_code', strtoupper($code))
                    ->with(['riskScore', 'economicCache', 'weatherCache', 'ports'])
                    ->firstOrFail();

        $risk     = $country->riskScore;
        $economic = $country->economicCache;
        $weather  = $country->weatherCache;

        $isWatched = Auth::check()
            ? Watchlist::where('user_id', Auth::id())
                       ->where('country_id', $country->id)
                       ->exists()
            : false;

        $formatBig = function (?float $val): string {
            if ($val === null || $val == 0) return 'N/A';
            if ($val >= 1_000_000_000_000) return '$' . round($val / 1_000_000_000_000, 2) . 'T';
            if ($val >= 1_000_000_000)     return '$' . round($val / 1_000_000_000, 2) . 'B';
            if ($val >= 1_000_000)         return '$' . round($val / 1_000_000, 2) . 'M';
            return '$' . number_format($val, 0);
        };

        $population = $economic?->population
            ? number_format((int) $economic->population)
            : 'N/A';

        $condition = $weather?->weather_condition ?? 'Unknown';

        return response()->json([
            'id'              => $country->id,
            'code'            => $country->country_code,
            'name'            => $country->country_name,
            'flag'            => "https://flagcdn.com/w80/{$country->country_code}.png",
            'flag_emoji'      => $country->flag,
            'region'          => $country->region,
            'capital'         => $country->capital,
            'currency'        => $country->currency_code,
            'risk_score'      => $risk?->total_score ?? 0,
            'risk_level'      => $risk?->risk_level ?? 'N/A',
            'risk_color'      => $risk?->risk_color ?? '#6B7280',
            'port_count'      => $country->ports->count(),
            // Economic
            'gdp'             => $economic?->formatted_gdp ?? 'N/A',
            'inflation'       => $economic?->inflation !== null ? round($economic->inflation, 2) . '%' : 'N/A',
            'population'      => $population,
            'exports'         => $formatBig($economic?->exports),
            'imports'         => $formatBig($economic?->imports),
            // Weather
            'weather_condition' => $condition,
            'weather_icon'    => $this->conditionToIcon($condition, $weather?->wind_speed ?? 0),
            'temperature'     => $weather?->temperature !== null ? $weather->temperature . '°C' : 'N/A',
            'wind_speed'      => $weather?->wind_speed   !== null ? $weather->wind_speed . ' km/h' : 'N/A',
            'rainfall'        => $weather ? round(max($weather->rainfall ?? 0, $weather->rain ?? 0), 1) . ' mm' : 'N/A',
            'is_watched'      => $isWatched,
            'detail_url'      => route('countries.show', $country->country_code),
            'compare_url'     => route('compare') . '?a=' . $country->country_code,
        ]);
    }

    // =====================================================
    // HELPERS
    // =====================================================

    /**
     * Map a weather condition string to an SVG icon name.
     */
    private function conditionToIcon(string $condition, float $windSpeed = 0): string
    {
        $c = strtolower($condition);

        if (str_contains($c, 'thunder') || str_contains($c, 'storm'))    return 'thunderstorm';
        if (str_contains($c, 'snow') || str_contains($c, 'blizzard'))    return 'snow';
        if (str_contains($c, 'fog')  || str_contains($c, 'mist'))        return 'fog';
        if (str_contains($c, 'drizzle'))                                  return 'drizzle';
        if (str_contains($c, 'rain') || str_contains($c, 'shower'))      return 'rain';
        if ($windSpeed > 50)                                              return 'wind';
        if (str_contains($c, 'cloud') || str_contains($c, 'overcast'))   return 'cloudy';
        if (str_contains($c, 'partly') || str_contains($c, 'few'))       return 'partly-cloudy';
        if (str_contains($c, 'clear') || str_contains($c, 'sunny'))      return 'clear';

        return 'partly-cloudy'; // default
    }

    /**
     * Map Open-Meteo WMO weather code to condition string.
     */
    private function codeToCondition(int $code): string
    {
        return match (true) {
            $code === 0              => 'Clear',
            in_array($code, [1,2,3]) => 'Partly Cloudy',
            in_array($code, [45,48]) => 'Fog',
            in_array($code, [51,53,55,56,57]) => 'Drizzle',
            in_array($code, [61,63,65,66,67]) => 'Rain',
            in_array($code, [71,73,75,77])    => 'Snow',
            in_array($code, [80,81,82])        => 'Rain Shower',
            in_array($code, [85,86])           => 'Snow Shower',
            in_array($code, [95,96,99])        => 'Thunderstorm',
            default                            => 'Cloudy',
        };
    }
}