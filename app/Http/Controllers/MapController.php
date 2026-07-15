<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\WeatherCache;
use App\Models\Watchlist;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    // Global port map view
    public function index()
    {
        $ports = Port::with('country')->get();
        return view('map.index', compact('ports'));
    }

    // AJAX: All countries with risk scores for choropleth map
    public function countriesData()
    {
        $countries = Country::with('riskScore')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'country_code', 'country_name', 'latitude', 'longitude', 'flag', 'region']);

        $data = $countries->map(function ($c) {
            $risk  = $c->riskScore;
            $level = $risk?->risk_level ?? 'Unknown';
            $score = $risk?->total_score ?? 0;

            $color = match ($level) {
                'Low'      => '#10B981',
                'Medium'   => '#F59E0B',
                'High'     => '#F97316',
                'Critical' => '#EF4444',
                default    => '#374151',
            };

            return [
                'code'     => $c->country_code,
                'name'     => $c->country_name,
                'lat'      => (float) $c->latitude,
                'lng'      => (float) $c->longitude,
                'flag'     => $c->flag,
                'region'   => $c->region,
                'score'    => $score,
                'level'    => $level,
                'color'    => $color,
            ];
        });

        return response()->json($data);
    }

    // AJAX: Country popup data
    public function countryPopup(string $code)
    {
        $country = Country::where('country_code', strtoupper($code))
                    ->with(['riskScore', 'economicCache', 'weatherCache'])
                    ->firstOrFail();

        $risk     = $country->riskScore;
        $economic = $country->economicCache;
        $weather  = $country->weatherCache;

        $isWatched = Auth::check()
            ? Watchlist::where('user_id', Auth::id())
                       ->where('country_id', $country->id)
                       ->exists()
            : false;

        // Format a large numeric value (GDP/Exports/Imports) into T/B/M string
        $formatBig = function (?float $val): string {
            if ($val === null || $val == 0) return 'N/A';
            if ($val >= 1_000_000_000_000) return '$' . round($val / 1_000_000_000_000, 2) . 'T';
            if ($val >= 1_000_000_000)     return '$' . round($val / 1_000_000_000, 2) . 'B';
            if ($val >= 1_000_000)         return '$' . round($val / 1_000_000, 2) . 'M';
            return '$' . number_format($val, 0);
        };

        // Population from economic_caches (falls back to N/A if missing)
        $population = $economic?->population
            ? number_format((int) $economic->population)
            : 'N/A';

        return response()->json([
            'id'           => $country->id,
            'code'         => $country->country_code,
            'name'         => $country->country_name,
            'flag'         => "https://flagcdn.com/w80/{$country->country_code}.png",
            'flag_emoji'   => $country->flag,
            'region'       => $country->region,
            'capital'      => $country->capital,
            'currency'     => $country->currency_code,
            'risk_score'   => $risk?->total_score ?? 0,
            'risk_level'   => $risk?->risk_level ?? 'N/A',
            'risk_color'   => $risk?->risk_color ?? '#6B7280',
            // Economic indicators — all from economic_caches
            'gdp'          => $economic?->formatted_gdp ?? 'N/A',
            'inflation'    => $economic?->inflation !== null ? round($economic->inflation, 2) . '%' : 'N/A',
            'population'   => $population,
            'exports'      => $formatBig($economic?->exports),
            'imports'      => $formatBig($economic?->imports),
            // Weather
            'temperature'  => $weather ? $weather->temperature . '°C' : 'N/A',
            'weather_icon' => $weather?->weather_icon ?? '🌡️',
            'is_watched'   => $isWatched,
            'detail_url'   => route('countries.show', $country->country_code),
            'compare_url'  => route('compare') . '?a=' . $country->country_code,
        ]);
    }
}