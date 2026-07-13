<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\NewsCache;
use App\Models\Port;

class DashboardController extends Controller
{
    public function index()
    {
        // =====================
        // SUMMARY STATS
        // =====================
        $totalCountries  = Country::count();
        $totalPorts      = Port::count();

        $avgRisk = RiskScore::avg('total_score') ?? 0;
        $avgRisk = round($avgRisk, 1);

        $highRiskCount = RiskScore::whereIn('risk_level', ['High', 'Critical'])->count();

        $criticalCount = RiskScore::where('risk_level', 'Critical')->count();

        // =====================
        // TOP RISK COUNTRIES
        // =====================
        $topRiskCountries = RiskScore::with('country')
            ->orderByDesc('total_score')
            ->limit(8)
            ->get();

        // =====================
        // RECENT NEWS
        // =====================
        $recentNews = NewsCache::with('country')
            ->latest()
            ->limit(5)
            ->get();

        // =====================
        // REGION RISK SUMMARY
        // =====================
        $regionRisk = Country::select('countries.region')
            ->selectRaw('AVG(risk_scores.total_score) as avg_risk')
            ->selectRaw('COUNT(countries.id) as country_count')
            ->leftJoin('risk_scores', 'countries.id', '=', 'risk_scores.country_id')
            ->groupBy('countries.region')
            ->whereNotNull('countries.region')
            ->orderByDesc('avg_risk')
            ->get();

        // =====================
        // COUNTRIES FOR MAP
        // =====================
        $countriesForMap = Country::with('riskScore')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'country_code', 'country_name', 'latitude', 'longitude', 'flag']);

        return view('dashboard.index', compact(
            'totalCountries',
            'totalPorts',
            'avgRisk',
            'highRiskCount',
            'criticalCount',
            'topRiskCountries',
            'recentNews',
            'regionRisk',
            'countriesForMap'
        ));
    }
}