<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\EconomicCache;
use App\Models\CurrencyCache;
use App\Models\RiskScore;
use App\Models\RiskHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DataVisualizationController extends Controller
{
    // =====================================================
    // MAIN VIEW
    // =====================================================

    public function index(Request $request)
    {
        $countryCode = $request->country ?? 'US';
        $period = $request->period ?? '30';

        $countries = Country::orderBy('country_name')->get(['country_code', 'country_name']);

        $selectedCountry = Country::where('country_code', $countryCode)->first();

        return view('data-visualization.index', compact('countries', 'countryCode', 'period', 'selectedCountry'));
    }

    // =====================================================
    // AJAX: Get GDP Trend Data
    // =====================================================

    public function getGdpTrend(Request $request)
    {
        $countryCode = $request->country ?? 'US';
        $period = (int)($request->period ?? 30);

        $country = Country::where('country_code', $countryCode)->first();
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        // Get economic data (simulated historical data)
        $data = $this->generateGdpTrendData($country, $period);

        return response()->json($data);
    }

    // =====================================================
    // AJAX: Get Inflation Trend Data
    // =====================================================

    public function getInflationTrend(Request $request)
    {
        $countryCode = $request->country ?? 'US';
        $period = (int)($request->period ?? 30);

        $country = Country::where('country_code', $countryCode)->first();
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        $data = $this->generateInflationTrendData($country, $period);

        return response()->json($data);
    }

    // =====================================================
    // AJAX: Get Currency Trend Data
    // =====================================================

    public function getCurrencyTrend(Request $request)
    {
        $countryCode = $request->country ?? 'US';
        $period = (int)($request->period ?? 30);

        $country = Country::where('country_code', $countryCode)->first();
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        $data = $this->generateCurrencyTrendData($country, $period);

        return response()->json($data);
    }

    // =====================================================
    // AJAX: Get Risk Trend Data
    // =====================================================

    public function getRiskTrend(Request $request)
    {
        $countryCode = $request->country ?? 'US';
        $period = (int)($request->period ?? 30);

        $country = Country::where('country_code', $countryCode)->first();
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        $data = $this->generateRiskTrendData($country, $period);

        return response()->json($data);
    }

    // =====================================================
    // PRIVATE HELPERS - Generate Simulated Data
    // =====================================================

    private function generateGdpTrendData(Country $country, int $days)
    {
        $economic = $country->economicCache;
        $currentGdp = $economic?->gdp ?? 1000000000000; // Default 1T

        $labels = [];
        $values = [];
        $dates = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            $dates[] = $date->format('Y-m-d');
            
            // Simulate GDP variation (±2% from current)
            $variation = (mt_rand(-200, 200) / 10000); // ±2%
            $value = $currentGdp * (1 + $variation - ($i * 0.00005));
            $values[] = round($value, 0);
        }

        $stats = [
            'current' => end($values),
            'high' => max($values),
            'low' => min($values),
            'average' => array_sum($values) / count($values),
            'change' => count($values) > 1 ? ((end($values) - $values[0]) / $values[0]) * 100 : 0,
        ];

        return [
            'labels' => $labels,
            'values' => $values,
            'dates' => $dates,
            'stats' => $stats,
            'currency' => $country->currency_code ?? 'USD',
        ];
    }

    private function generateInflationTrendData(Country $country, int $days)
    {
        $economic = $country->economicCache;
        $currentInflation = $economic?->inflation ?? 3.5;

        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            
            // Simulate inflation variation
            $variation = (mt_rand(-50, 50) / 100);
            $value = $currentInflation + $variation - ($i * 0.001);
            $values[] = round($value, 2);
        }

        $stats = [
            'current' => end($values),
            'high' => max($values),
            'low' => min($values),
            'average' => array_sum($values) / count($values),
            'change' => count($values) > 1 ? ((end($values) - $values[0]) / $values[0]) * 100 : 0,
        ];

        return [
            'labels' => $labels,
            'values' => $values,
            'stats' => $stats,
        ];
    }

    private function generateCurrencyTrendData(Country $country, int $days)
    {
        $currencyCache = $country->currencyCache;
        $currentRate = $currencyCache?->exchange_rate ?? 1.0;

        if ($country->currency_code === 'USD') {
            $currentRate = 1.0;
        }

        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            
            // Simulate rate variation
            $variation = (mt_rand(-100, 100) / 10000);
            $value = $currentRate * (1 + $variation);
            $values[] = round($value, 4);
        }

        $stats = [
            'current' => end($values),
            'high' => max($values),
            'low' => min($values),
            'average' => array_sum($values) / count($values),
            'change' => count($values) > 1 ? ((end($values) - $values[0]) / $values[0]) * 100 : 0,
        ];

        return [
            'labels' => $labels,
            'values' => $values,
            'stats' => $stats,
            'currency' => $country->currency_code ?? 'USD',
        ];
    }

    private function generateRiskTrendData(Country $country, int $days)
    {
        $riskScore = $country->riskScore;
        $currentRisk = $riskScore?->total_score ?? 50;

        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            
            // Simulate risk variation
            $variation = (mt_rand(-300, 300) / 100);
            $value = max(0, min(100, $currentRisk + $variation - ($i * 0.05)));
            $values[] = round($value, 1);
        }

        $stats = [
            'current' => end($values),
            'high' => max($values),
            'low' => min($values),
            'average' => array_sum($values) / count($values),
            'change' => count($values) > 1 ? ((end($values) - $values[0]) / $values[0]) * 100 : 0,
        ];

        return [
            'labels' => $labels,
            'values' => $values,
            'stats' => $stats,
            'level' => $riskScore?->risk_level ?? 'Medium',
        ];
    }
}
