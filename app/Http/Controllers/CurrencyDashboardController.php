<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CurrencyCache;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CurrencyDashboardController extends Controller
{
    public function __construct(
        private readonly CurrencyService $currencyService
    ) {}

    // =====================
    // INDEX - Currency Dashboard
    // =====================

    public function index(Request $request)
    {
        $countryA = $request->country_a;
        $countryB = $request->country_b;
        
        $countries = Country::whereNotNull('currency_code')
            ->orderBy('country_name')
            ->get(['country_code', 'country_name', 'currency_code', 'currency_name']);

        // If both countries selected
        $comparison = null;
        $chartData = null;
        
        if ($countryA && $countryB) {
            $comparison = $this->getComparison($countryA, $countryB);
            $chartData = $this->getChartData($countryA, $countryB, $request->period ?? '30');
        }

        return view('currency.dashboard', compact('countries', 'comparison', 'chartData', 'countryA', 'countryB'));
    }

    // =====================
    // AJAX - Get Comparison Data
    // =====================

    public function getComparison(string $codeA, string $codeB)
    {
        $countryA = Country::where('country_code', strtoupper($codeA))->first();
        $countryB = Country::where('country_code', strtoupper($codeB))->first();

        if (!$countryA || !$countryB) {
            return null;
        }

        // Get exchange rates from cache or API
        $rates = Cache::remember('currency_rates_usd', 3600, function () {
            return $this->currencyService->getRates();
        });

        if (!$rates || !isset($rates['rates'])) {
            return null;
        }

        $rateA = $rates['rates'][$countryA->currency_code] ?? null;
        $rateB = $rates['rates'][$countryB->currency_code] ?? null;

        if (!$rateA || !$rateB) {
            return null;
        }

        // Calculate cross rate
        $crossRate = $rateB / $rateA;
        $inverseCrossRate = $rateA / $rateB;

        return [
            'country_a' => [
                'name' => $countryA->country_name,
                'code' => $countryA->country_code,
                'currency_name' => $countryA->currency_name,
                'currency_code' => $countryA->currency_code,
                'rate_to_usd' => $rateA,
            ],
            'country_b' => [
                'name' => $countryB->country_name,
                'code' => $countryB->country_code,
                'currency_name' => $countryB->currency_name,
                'currency_code' => $countryB->currency_code,
                'rate_to_usd' => $rateB,
            ],
            'cross_rate' => $crossRate,
            'inverse_cross_rate' => $inverseCrossRate,
            'last_updated' => $rates['time_last_update_utc'] ?? now()->toDateTimeString(),
        ];
    }

    // =====================
    // AJAX - Get Chart Data
    // =====================

    public function getChartData(string $codeA, string $codeB, int $days = 30)
    {
        $countryA = Country::where('country_code', strtoupper($codeA))->first();
        $countryB = Country::where('country_code', strtoupper($codeB))->first();

        if (!$countryA || !$countryB) {
            return null;
        }

        // Generate historical data (simulated for demo)
        // In production, you would fetch real historical data from an API
        $data = $this->generateHistoricalData($countryA->currency_code, $countryB->currency_code, $days);

        return $data;
    }

    // =====================
    // AJAX - Refresh Comparison
    // =====================

    public function refreshComparison(Request $request)
    {
        $countryA = $request->country_a;
        $countryB = $request->country_b;
        $period = $request->period ?? '30';

        if (!$countryA || !$countryB) {
            return response()->json(['error' => 'Both countries are required'], 400);
        }

        $comparison = $this->getComparison($countryA, $countryB);
        $chartData = $this->getChartData($countryA, $countryB, (int)$period);

        if (!$comparison || !$chartData) {
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }

        return response()->json([
            'success' => true,
            'comparison' => $comparison,
            'chartData' => $chartData,
        ]);
    }

    // =====================
    // PRIVATE HELPERS
    // =====================

    private function generateHistoricalData(string $currencyA, string $currencyB, int $days)
    {
        // Get current rate
        $rates = Cache::remember('currency_rates_usd', 3600, function () {
            return $this->currencyService->getRates();
        });

        $rateA = $rates['rates'][$currencyA] ?? 1;
        $rateB = $rates['rates'][$currencyB] ?? 1;
        $currentRate = $rateB / $rateA;

        $labels = [];
        $values = [];
        $high = $currentRate;
        $low = $currentRate;
        $sum = 0;

        // Generate data for the last N days
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            
            // Simulate rate with small random variation
            $variation = (mt_rand(-100, 100) / 10000); // ±1% variation
            $rate = $currentRate * (1 + $variation - ($i * 0.0001)); // slight trend
            $values[] = round($rate, 6);
            
            $high = max($high, $rate);
            $low = min($low, $rate);
            $sum += $rate;
        }

        $average = $sum / count($values);
        $dailyChange = count($values) > 1 ? (($values[count($values) - 1] - $values[count($values) - 2]) / $values[count($values) - 2]) * 100 : 0;
        $weeklyChange = count($values) >= 7 ? (($values[count($values) - 1] - $values[count($values) - 7]) / $values[count($values) - 7]) * 100 : 0;
        $monthlyChange = (($values[count($values) - 1] - $values[0]) / $values[0]) * 100;

        return [
            'labels' => $labels,
            'values' => $values,
            'stats' => [
                'current' => round($currentRate, 6),
                'high' => round($high, 6),
                'low' => round($low, 6),
                'average' => round($average, 6),
                'daily_change' => round($dailyChange, 2),
                'weekly_change' => round($weeklyChange, 2),
                'monthly_change' => round($monthlyChange, 2),
            ],
        ];
    }
}
