<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\WeatherCache;
use App\Models\EconomicCache;
use App\Models\CurrencyCache;
use App\Models\NewsCache;
use App\Models\Watchlist;
use App\Services\WeatherService;
use App\Services\EconomicService;
use App\Services\CurrencyService;
use App\Services\NewsService;
use App\Services\SentimentService;
use App\Services\RiskScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CountryController extends Controller
{
    public function __construct(
        private readonly WeatherService     $weatherService,
        private readonly EconomicService    $economicService,
        private readonly CurrencyService    $currencyService,
        private readonly NewsService        $newsService,
        private readonly SentimentService   $sentimentService,
        private readonly RiskScoringService $riskScoringService
    ) {}

    // =====================
    // INDEX - Country List
    // =====================

    public function index(Request $request)
    {
        $search = $request->search;

        $countries = Country::with('riskScore')
            ->when($search, fn($q) =>
                $q->where('country_name', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%")
                  ->orWhere('currency_code', 'like', "%{$search}%")
            )
            ->orderBy('country_name')
            ->paginate(15)
            ->withQueryString();

        // AJAX search returns JSON for topbar autocomplete
        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(
                Country::when($search, fn($q) =>
                    $q->where('country_name', 'like', "%{$search}%")
                      ->orWhere('region', 'like', "%{$search}%")
                )
                ->orderBy('country_name')
                ->limit(10)
                ->get(['country_code', 'country_name', 'region', 'flag'])
            );
        }

        return view('dashboard.countries', compact('countries'));
    }

    // =====================
    // SHOW - Country Detail
    // =====================

    public function show(string $code)
    {
        $country = Country::where('country_code', strtoupper($code))
                    ->orWhere('country_code3', strtoupper($code))
                    ->firstOrFail();

        // ---- WEATHER (cache 6 hours) ----
        $weather = WeatherCache::where('country_id', $country->id)->latest()->first();
        if (!$weather && $country->latitude && $country->longitude) {
            $rawWeather = $this->weatherService->getWeather($country->latitude, $country->longitude);
            if ($rawWeather) {
                $weather = WeatherCache::updateOrCreate(
                    ['country_id' => $country->id],
                    [
                        'temperature'       => $rawWeather['temperature_2m'] ?? null,
                        'rainfall'          => $rawWeather['rain'] ?? 0,
                        'rain'              => $rawWeather['rain'] ?? 0,
                        'wind_speed'        => $rawWeather['wind_speed_10m'] ?? null,
                        'humidity'          => $rawWeather['relative_humidity_2m'] ?? null,
                        'weather_code'      => $rawWeather['weather_code'] ?? null,
                        'weather_condition' => $this->weatherCodeToCondition($rawWeather['weather_code'] ?? 0),
                        'storm_risk'        => $this->calcStormRisk($rawWeather),
                        'weather_date'      => now(),
                    ]
                );
            }
        }

        // ---- ECONOMIC (cache 24 hours) ----
        $economic = EconomicCache::where('country_id', $country->id)->latest('year')->first();
        if (!$economic) {
            $gdp       = $this->economicService->getIndicator($country->country_code3, 'NY.GDP.MKTP.CD');
            $inflation = $this->economicService->getIndicator($country->country_code3, 'FP.CPI.TOTL.ZG');
            if ($gdp !== null || $inflation !== null) {
                $economic = EconomicCache::updateOrCreate(
                    ['country_id' => $country->id, 'year' => now()->year - 1],
                    [
                        'gdp'       => $gdp,
                        'inflation' => $inflation,
                    ]
                );
            }
        }

        // ---- CURRENCY (cache 12 hours) ----
        $currency = CurrencyCache::where('country_id', $country->id)->latest()->first();
        if (!$currency && $country->currency_code) {
            $rates = $this->currencyService->getRates();
            if ($rates && isset($rates['rates'][$country->currency_code])) {
                $rate = $rates['rates'][$country->currency_code];
                $currency = CurrencyCache::updateOrCreate(
                    ['country_id' => $country->id],
                    [
                        'base_currency'   => 'USD',
                        'target_currency' => $country->currency_code,
                        'exchange_rate'   => $rate,
                        'rate_change_pct' => 0,
                        'rate_date'       => now(),
                    ]
                );
            }
        }

        // ---- NEWS (cache 4 hours) ----
        $news = NewsCache::where('country_id', $country->id)->latest()->limit(10)->get();
        if ($news->isEmpty()) {
            $rawNews = $this->newsService->getNews();
            $articles = $rawNews['articles'] ?? [];
            if (!empty($articles)) {
                $analyzed = $this->sentimentService->analyzeMultiple($articles);
                foreach ($analyzed['articles'] as $article) {
                    NewsCache::create([
                        'country_id'      => $country->id,
                        'title'           => $article['title'] ?? 'No Title',
                        'description'     => $article['description'] ?? null,
                        'url'             => $article['url'] ?? '#',
                        'image'           => $article['image'] ?? null,
                        'source'          => $article['source']['name'] ?? null,
                        'published_at'    => isset($article['publishedAt']) ? \Carbon\Carbon::parse($article['publishedAt']) : now(),
                        'sentiment'       => $article['sentiment'],
                        'sentiment_score' => $article['sentiment_score'],
                    ]);
                }
                $news = NewsCache::where('country_id', $country->id)->latest()->limit(10)->get();
            }
        }

        // ---- RISK SCORE ----
        $riskScore = RiskScore::where('country_id', $country->id)->first();
        if (!$riskScore) {
            $riskScore = $this->riskScoringService->calculate($country);
        }

        // ---- RECOMMENDATION ----
        $recommendation = $this->riskScoringService->getRecommendation($riskScore, $country);

        // ---- PORTS ----
        $ports = $country->ports()->limit(10)->get();

        // ---- WATCHLIST STATUS ----
        $isWatched = Auth::check()
            ? Watchlist::where('user_id', Auth::id())
                       ->where('country_id', $country->id)
                       ->exists()
            : false;

        // ---- ECONOMIC HISTORY (last 5 years for charts) ----
        $economicHistory = EconomicCache::where('country_id', $country->id)
            ->orderByDesc('year')
            ->limit(5)
            ->get()
            ->reverse()
            ->values();

        return view('countries.show', compact(
            'country',
            'weather',
            'economic',
            'currency',
            'news',
            'riskScore',
            'recommendation',
            'ports',
            'isWatched',
            'economicHistory'
        ));
    }

    // =====================
    // AJAX - Risk Data
    // =====================

    public function riskData(string $code)
    {
        $country = Country::where('country_code', strtoupper($code))->firstOrFail();
        $risk    = RiskScore::where('country_id', $country->id)->first();

        return response()->json([
            'country_code' => $country->country_code,
            'country_name' => $country->country_name,
            'total_score'  => $risk?->total_score ?? 0,
            'risk_level'   => $risk?->risk_level ?? 'Unknown',
            'risk_color'   => $risk?->risk_color ?? '#6B7280',
            'weather_score'   => $risk?->weather_score ?? 0,
            'inflation_score' => $risk?->inflation_score ?? 0,
            'currency_score'  => $risk?->currency_score ?? 0,
            'news_score'      => $risk?->news_score ?? 0,
        ]);
    }

    // =====================
    // PRIVATE HELPERS
    // =====================

    private function weatherCodeToCondition(int $code): string
    {
        if ($code === 0)            return 'Clear Sky';
        if ($code <= 3)             return 'Partly Cloudy';
        if ($code <= 48)            return 'Foggy';
        if ($code <= 55)            return 'Drizzle';
        if ($code <= 67)            return 'Rain';
        if ($code <= 77)            return 'Snow';
        if ($code <= 82)            return 'Rain Showers';
        if ($code <= 99)            return 'Thunderstorm';
        return 'Unknown';
    }

    private function calcStormRisk(array $weather): int
    {
        $code  = $weather['weather_code'] ?? 0;
        $wind  = $weather['wind_speed_10m'] ?? 0;
        $score = 0;

        if ($code >= 80) $score += 5;
        if ($wind > 60)  $score += 3;
        if ($wind > 40)  $score += 2;

        return min(10, $score);
    }
}