<?php

namespace App\Services;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\WeatherCache;
use App\Models\EconomicCache;
use App\Models\CurrencyCache;
use App\Models\NewsCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RiskScoringService
{
    public function __construct(
        private readonly SentimentService $sentimentService
    ) {}

    // =====================================================
    // MAIN: CALCULATE & STORE RISK SCORE FOR A COUNTRY
    // =====================================================

    /**
     * Calculate and persist the risk score for a country.
     * Every country MUST receive a score — never skip.
     * Returns the saved RiskScore model.
     */
    public function calculate(Country $country): RiskScore
    {
        $weatherScore   = $this->calcWeatherRisk($country);
        $inflationScore = $this->calcInflationRisk($country);
        $currencyScore  = $this->calcCurrencyRisk($country);
        $newsScore      = $this->calcNewsRisk($country);

        // Weighted total: 30% weather + 20% inflation + 10% currency + 40% news
        $total = ($weatherScore * 0.30)
               + ($inflationScore * 0.20)
               + ($currencyScore  * 0.10)
               + ($newsScore      * 0.40);

        $total = round(min(100, max(0, $total)), 2);

        $level = RiskScore::levelFromScore($total);

        // Upsert risk score
        $riskScore = RiskScore::updateOrCreate(
            ['country_id' => $country->id],
            [
                'weather_score'   => round($weatherScore, 2),
                'inflation_score' => round($inflationScore, 2),
                'currency_score'  => round($currencyScore, 2),
                'news_score'      => round($newsScore, 2),
                'total_score'     => $total,
                'risk_level'      => $level,
                'calculated_at'   => now(),
            ]
        );

        return $riskScore;
    }

    // =====================================================
    // WEATHER RISK (30%)  →  Score 0–100
    // =====================================================

    private function calcWeatherRisk(Country $country): float
    {
        // Try latest weather cache for this country
        $weather = WeatherCache::where('country_id', $country->id)
                    ->latest()
                    ->first();

        if (!$weather) {
            // Default: assume mild weather conditions (low-moderate risk)
            // Countries without coordinates or API data get a neutral default
            return 20.0;
        }

        return $weather->weather_risk_score;
    }

    // =====================================================
    // INFLATION RISK (20%)  →  Score 0–100
    // =====================================================

    private function calcInflationRisk(Country $country): float
    {
        $economic = EconomicCache::where('country_id', $country->id)
                    ->orderByDesc('year')
                    ->first();

        // Default: moderate inflation (~3–5%) assumed for unknown countries
        if (!$economic || $economic->inflation === null) {
            return 35.0;
        }

        $inflation = (float) $economic->inflation;

        // Scoring logic:
        // <=0%:   deflation — also risky (50)
        // 0–2%:   low risk (10)
        // 2–5%:   moderate (30)
        // 5–10%:  high (55)
        // 10–20%: very high (75)
        // >20%:   critical (95)
        if ($inflation <= 0)    return 50.0;  // deflation is also risky
        if ($inflation <= 2)    return 10.0;
        if ($inflation <= 5)    return 30.0;
        if ($inflation <= 10)   return 55.0;
        if ($inflation <= 20)   return 75.0;
        return 95.0;
    }

    // =====================================================
    // CURRENCY RISK (10%)  →  Score 0–100
    // =====================================================

    private function calcCurrencyRisk(Country $country): float
    {
        $currency = CurrencyCache::where('country_id', $country->id)
                    ->latest()
                    ->first();

        // Default: assume moderate-stable exchange rate for unknown countries
        if (!$currency) {
            return 25.0;
        }

        $changePct = abs((float) ($currency->rate_change_pct ?? 0));

        // Currency volatility scoring:
        // <1%:   stable (10)
        // 1–3%:  moderate (30)
        // 3–7%:  high (60)
        // >7%:   critical (85)
        if ($changePct <= 1) return 10.0;
        if ($changePct <= 3) return 30.0;
        if ($changePct <= 7) return 60.0;
        return 85.0;
    }

    // =====================================================
    // NEWS SENTIMENT RISK (40%)  →  Score 0–100
    // =====================================================

    private function calcNewsRisk(Country $country): float
    {
        // First: try country-specific news
        $newsCaches = NewsCache::where('country_id', $country->id)
                        ->latest()
                        ->limit(20)
                        ->get();

        // Fallback: use pre-computed global news sentiment (cached for performance)
        if ($newsCaches->isEmpty()) {
            return $this->getGlobalNewsFallbackScore();
        }

        $articles = $newsCaches->map(fn($n) => [
            'title'       => $n->title ?? '',
            'description' => $n->description ?? '',
        ])->toArray();

        $result = $this->sentimentService->analyzeMultiple($articles);

        return $result['risk_score'];
    }

    /**
     * Compute or retrieve a cached global news sentiment score.
     * Uses all news items (regardless of country_id) as a global baseline.
     * Cached for 1 hour so it's computed once per batch run.
     */
    private function getGlobalNewsFallbackScore(): float
    {
        return Cache::remember('risk.global_news_fallback', 3600, function () {
            // Use all news (any country or null country_id) as global baseline
            $allNews = NewsCache::latest()->limit(50)->get();

            if ($allNews->isEmpty()) {
                // Neutral-moderate default: global trade has normal risk level
                return 40.0;
            }

            $articles = $allNews->map(fn($n) => [
                'title'       => $n->title ?? '',
                'description' => $n->description ?? '',
            ])->toArray();

            $result = $this->sentimentService->analyzeMultiple($articles);
            return $result['risk_score'];
        });
    }

    // =====================================================
    // BATCH CALCULATE FOR ALL COUNTRIES
    // =====================================================

    /**
     * Recalculate and persist risk scores for ALL countries.
     * Every country gets a score — no country is skipped.
     * Returns a summary array with totals.
     */
    public function calculateAll(): array
    {
        // Clear the global news fallback cache so it's fresh for this run
        Cache::forget('risk.global_news_fallback');

        $scored  = 0;
        $failed  = 0;
        $results = [];

        // Eager-load all cache tables to avoid N+1
        $countries = Country::with([
            'weatherCache',
            'economicCache',
            'currencyCache',
            'newsCaches',
        ])->get();

        foreach ($countries as $country) {
            try {
                $riskScore = $this->calculate($country);
                $results[] = [
                    'country'     => $country->country_name,
                    'total_score' => $riskScore->total_score,
                    'risk_level'  => $riskScore->risk_level,
                ];
                $scored++;
            } catch (\Throwable $e) {
                $results[] = [
                    'country'     => $country->country_name,
                    'total_score' => null,
                    'risk_level'  => 'Error: ' . $e->getMessage(),
                ];
                $failed++;
            }
        }

        $results['_summary'] = [
            'total'   => $countries->count(),
            'scored'  => $scored,
            'failed'  => $failed,
        ];

        return $results;
    }

    // =====================================================
    // GET RECOMMENDATION TEXT
    // =====================================================

    public function getRecommendation(RiskScore $riskScore, Country $country): array
    {
        $reasons = [];

        // Weather reasons
        if ($riskScore->weather_score <= 20) {
            $reasons['positive'][] = 'Stable weather conditions';
        } elseif ($riskScore->weather_score >= 60) {
            $reasons['negative'][] = 'Severe weather risk detected';
        }

        // Inflation reasons
        if ($riskScore->inflation_score <= 20) {
            $reasons['positive'][] = 'Low and stable inflation';
        } elseif ($riskScore->inflation_score >= 60) {
            $reasons['negative'][] = 'High inflation rate';
        }

        // Currency reasons
        if ($riskScore->currency_score <= 15) {
            $reasons['positive'][] = 'Stable exchange rate';
        } elseif ($riskScore->currency_score >= 50) {
            $reasons['negative'][] = 'High currency volatility';
        }

        // News reasons
        if ($riskScore->news_score <= 25) {
            $reasons['positive'][] = 'Positive news sentiment';
        } elseif ($riskScore->news_score >= 60) {
            $reasons['negative'][] = 'Negative news sentiment';
        }

        $action = match ($riskScore->risk_level) {
            'Low'      => 'Recommended for Import',
            'Medium'   => 'Use with Caution',
            'High'     => 'High Risk — Review Before Proceeding',
            'Critical' => 'Critical Risk — Not Recommended',
            default    => 'Assessment Pending',
        };

        return [
            'action'   => $action,
            'level'    => $riskScore->risk_level,
            'score'    => $riskScore->total_score,
            'positive' => $reasons['positive'] ?? [],
            'negative' => $reasons['negative'] ?? [],
            'color'    => $riskScore->risk_color,
        ];
    }
}
