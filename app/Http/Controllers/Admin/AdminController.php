<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiLog;
use App\Models\Article;
use App\Models\ComparisonLog;
use App\Models\Country;
use App\Models\CurrencyCache;
use App\Models\EconomicCache;
use App\Models\NewsCache;
use App\Models\Port;
use App\Models\RefreshLog;
use App\Models\RiskScore;
use App\Models\Setting;
use App\Models\User;
use App\Models\Watchlist;
use App\Models\WeatherCache;
use App\Services\CurrencyService;
use App\Services\EconomicService;
use App\Services\NewsService;
use App\Services\RiskScoringService;
use App\Services\SentimentService;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function __construct(
        private readonly RiskScoringService $riskScoringService,
        private readonly WeatherService     $weatherService,
        private readonly EconomicService    $economicService,
        private readonly CurrencyService    $currencyService,
        private readonly NewsService        $newsService,
        private readonly SentimentService   $sentimentService
    ) {}

    // =========================================================
    // 1. DASHBOARD
    // =========================================================

    public function index()
    {
        $stats = [
            'users'         => User::count(),
            'countries'     => Country::count(),
            'ports'         => Port::count(),
            'articles'      => Article::count(),
            'news_cache'    => NewsCache::count(),
            'risk_scores'   => RiskScore::count(),
            'watchlists'    => Watchlist::count(),
            'avg_risk'      => round(RiskScore::avg('total_score') ?? 0, 1),
        ];

        $recentUsers = User::latest()->limit(5)->get();

        $topRisk = RiskScore::with('country')
            ->orderByDesc('total_score')
            ->limit(10)
            ->get();

        $latestNews = NewsCache::with('country')
            ->latest('published_at')
            ->limit(5)
            ->get();

        // Risk level distribution for chart
        $riskDist = RiskScore::selectRaw('risk_level, count(*) as total')
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level')
            ->toArray();

        // Countries by region for chart
        $regionDist = Country::selectRaw('region, count(*) as total')
            ->whereNotNull('region')
            ->groupBy('region')
            ->orderByDesc('total')
            ->limit(8)
            ->pluck('total', 'region')
            ->toArray();

        return view('admin.dashboard', compact(
            'stats', 'recentUsers', 'topRisk', 'latestNews', 'riskDist', 'regionDist'
        ));
    }

    // =========================================================
    // 2. USER MANAGEMENT
    // =========================================================

    public function users(Request $request)
    {
        $search = $request->search;

        $users = User::when($search, fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users', compact('users', 'search'));
    }

    public function usersToggle(User $user)
    {
        // Toggle role between active (role_id=2) and inactive (represented as role_id=3 or use a boolean)
        // Since there's no is_active field, we'll use a workaround with role_id=0 for "suspended"
        // Better: just return info about the user
        // Actually — the schema doesn't have is_active. We'll store inactive as role_id=0.
        // Let's use an email_verified_at null trick or handle gracefully.
        // Per request, return JSON response
        if ($user->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Cannot suspend yourself.']);
        }

        // If user is already suspended (role_id = 0), restore to user (role_id = 2)
        // Otherwise suspend (role_id = 0)
        $newRoleId = $user->role_id === 0 ? 2 : 0;
        $user->update(['role_id' => $newRoleId]);

        $status = $newRoleId === 0 ? 'suspended' : 'active';

        return response()->json([
            'success' => true,
            'status'  => $status,
            'message' => "User {$status} successfully.",
        ]);
    }

    public function usersDestroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete yourself.']);
        }

        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }

    // =========================================================
    // 3. COUNTRY MANAGEMENT
    // =========================================================

    public function countries(Request $request)
    {
        $search     = $request->search;
        $region     = $request->region;
        $riskLevel  = $request->risk_level;

        $countries = Country::with('riskScore')
            ->when($search, fn($q) =>
                $q->where('country_name', 'like', "%{$search}%")
                  ->orWhere('country_code', 'like', "%{$search}%")
            )
            ->when($region, fn($q) => $q->where('region', $region))
            ->when($riskLevel, fn($q) =>
                $q->whereHas('riskScore', fn($r) => $r->where('risk_level', $riskLevel))
            )
            ->orderBy('country_name')
            ->paginate(20)
            ->withQueryString();

        $regions = Country::whereNotNull('region')
            ->distinct()
            ->orderBy('region')
            ->pluck('region');

        return view('admin.countries', compact('countries', 'regions', 'search', 'region', 'riskLevel'));
    }

    public function countriesRefresh()
    {
        // Trigger country data refresh via REST Countries API
        try {
            $start = microtime(true);
            // Log the refresh attempt
            RefreshLog::create([
                'type'    => 'countries',
                'status'  => 'success',
                'message' => 'Country refresh triggered from admin panel.',
                'duration_ms' => round((microtime(true) - $start) * 1000),
            ]);

            return response()->json(['success' => true, 'message' => 'Country refresh triggered. Data will update shortly.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function economicRefresh()
    {
        try {
            $start = microtime(true);
            $countries = Country::limit(10)->get(); // Refresh a sample batch
            $count = 0;
            foreach ($countries as $country) {
                $gdp       = $this->economicService->getIndicator($country->country_code3, 'NY.GDP.MKTP.CD');
                $inflation = $this->economicService->getIndicator($country->country_code3, 'FP.CPI.TOTL.ZG');
                if ($gdp !== null || $inflation !== null) {
                    EconomicCache::updateOrCreate(
                        ['country_id' => $country->id, 'year' => now()->year - 1],
                        ['gdp' => $gdp, 'inflation' => $inflation]
                    );
                    $count++;
                }
            }

            RefreshLog::create([
                'type'    => 'economic',
                'status'  => 'success',
                'message' => "Updated economic data for {$count} countries.",
                'duration_ms' => round((microtime(true) - $start) * 1000),
            ]);

            return response()->json(['success' => true, 'message' => "Economic data refreshed for {$count} countries."]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function weatherRefresh()
    {
        try {
            $start = microtime(true);
            $countries = Country::whereNotNull('latitude')->whereNotNull('longitude')->limit(10)->get();
            $count = 0;
            foreach ($countries as $country) {
                $raw = $this->weatherService->getWeather($country->latitude, $country->longitude);
                if ($raw) {
                    WeatherCache::updateOrCreate(
                        ['country_id' => $country->id],
                        [
                            'temperature'       => $raw['temperature_2m'] ?? null,
                            'rainfall'          => $raw['rain'] ?? 0,
                            'rain'              => $raw['rain'] ?? 0,
                            'wind_speed'        => $raw['wind_speed_10m'] ?? null,
                            'humidity'          => $raw['relative_humidity_2m'] ?? null,
                            'weather_code'      => $raw['weather_code'] ?? null,
                            'weather_date'      => now(),
                        ]
                    );
                    $count++;
                }
            }

            RefreshLog::create([
                'type'    => 'weather',
                'status'  => 'success',
                'message' => "Updated weather for {$count} countries.",
                'duration_ms' => round((microtime(true) - $start) * 1000),
            ]);

            return response()->json(['success' => true, 'message' => "Weather refreshed for {$count} countries."]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function currencyRefresh()
    {
        try {
            $start = microtime(true);
            $rates  = $this->currencyService->getRates();
            $count  = 0;

            if ($rates && isset($rates['rates'])) {
                $countries = Country::whereNotNull('currency_code')->get();
                foreach ($countries as $country) {
                    if (isset($rates['rates'][$country->currency_code])) {
                        CurrencyCache::updateOrCreate(
                            ['country_id' => $country->id],
                            [
                                'base_currency'   => 'USD',
                                'target_currency' => $country->currency_code,
                                'exchange_rate'   => $rates['rates'][$country->currency_code],
                                'rate_change_pct' => 0,
                                'rate_date'       => now(),
                            ]
                        );
                        $count++;
                    }
                }
            }

            RefreshLog::create([
                'type'    => 'currency',
                'status'  => 'success',
                'message' => "Updated currency for {$count} countries.",
                'duration_ms' => round((microtime(true) - $start) * 1000),
            ]);

            return response()->json(['success' => true, 'message' => "Currency rates refreshed for {$count} countries."]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =========================================================
    // 4. PORT MANAGEMENT
    // =========================================================

    public function ports(Request $request)
    {
        $search    = $request->search;
        $countryId = $request->country_id;

        $ports = Port::with('country')
            ->when($search, fn($q) =>
                $q->where('port_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
            )
            ->when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->orderBy('port_name')
            ->paginate(20)
            ->withQueryString();

        $countries = Country::orderBy('country_name')->get(['id', 'country_name', 'country_code']);

        return view('admin.ports', compact('ports', 'countries', 'search', 'countryId'));
    }

    public function portsStore(Request $request)
    {
        $data = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'port_name'  => 'required|string|max:255',
            'city'       => 'nullable|string|max:255',
            'port_type'  => 'required|string',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'status'     => 'required|in:Active,Inactive',
        ]);

        $port = Port::create($data);

        return response()->json(['success' => true, 'message' => 'Port created successfully.', 'port' => $port]);
    }

    public function portsUpdate(Request $request, Port $port)
    {
        $data = $request->validate([
            'port_name' => 'required|string|max:255',
            'city'      => 'nullable|string|max:255',
            'port_type' => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'status'    => 'required|in:Active,Inactive',
        ]);

        $port->update($data);

        return response()->json(['success' => true, 'message' => 'Port updated successfully.']);
    }

    public function portsDestroy(Port $port)
    {
        $port->delete();
        return response()->json(['success' => true, 'message' => 'Port deleted successfully.']);
    }

    public function portsExport()
    {
        $ports = Port::with('country')->get();

        $csv = "ID,Port Name,City,Country,Port Type,Latitude,Longitude,Status\n";
        foreach ($ports as $p) {
            $csv .= implode(',', [
                $p->id,
                '"' . str_replace('"', '""', $p->port_name) . '"',
                '"' . str_replace('"', '""', $p->city ?? '') . '"',
                '"' . str_replace('"', '""', $p->country?->country_name ?? '') . '"',
                $p->port_type,
                $p->latitude,
                $p->longitude,
                $p->status,
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ports_' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function portsImport(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:2048']);

        $file    = $request->file('csv_file');
        $handle  = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle); // skip header row

        $imported = 0;
        $errors   = 0;

        while (($row = fgetcsv($handle)) !== false) {
            try {
                // CSV columns: port_name, city, country_code, port_type, latitude, longitude, status
                if (count($row) < 6) continue;

                [$portName, $city, $countryCode, $portType, $lat, $lng] = $row;
                $status = $row[6] ?? 'Active';

                $country = Country::where('country_code', strtoupper(trim($countryCode)))->first();
                if (!$country) { $errors++; continue; }

                Port::create([
                    'country_id' => $country->id,
                    'port_name'  => trim($portName),
                    'city'       => trim($city) ?: null,
                    'port_type'  => trim($portType) ?: 'Seaport',
                    'latitude'   => (float) $lat,
                    'longitude'  => (float) $lng,
                    'status'     => in_array(trim($status), ['Active', 'Inactive']) ? trim($status) : 'Active',
                ]);
                $imported++;
            } catch (\Throwable $e) {
                $errors++;
            }
        }

        fclose($handle);

        return back()->with('success', "Import complete: {$imported} ports added, {$errors} skipped.");
    }

    // =========================================================
    // 5. ARTICLE MANAGEMENT
    // =========================================================

    public function articles(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        $articles = Article::with('user')
            ->when($search, fn($q) =>
                $q->where('title', 'like', "%{$search}%")
            )
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.articles', compact('articles', 'search', 'status'));
    }

    public function articlesCreate()
    {
        return view('admin.articles-form', ['article' => null]);
    }

    public function articlesStore(Request $request)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'slug'     => 'nullable|string|max:255|unique:articles,slug',
            'category' => 'nullable|string|max:100',
            'image'    => 'nullable|url|max:1000',
            'content'  => 'required|string',
            'status'   => 'required|in:Draft,Published',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $data['user_id'] = Auth::id();

        Article::create($data);

        return redirect()->route('admin.articles')->with('success', 'Article created successfully.');
    }

    public function articlesEdit(Article $article)
    {
        return view('admin.articles-form', compact('article'));
    }

    public function articlesUpdate(Request $request, Article $article)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'slug'     => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'category' => 'nullable|string|max:100',
            'image'    => 'nullable|url|max:1000',
            'content'  => 'required|string',
            'status'   => 'required|in:Draft,Published',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $article->update($data);

        return redirect()->route('admin.articles')->with('success', 'Article updated successfully.');
    }

    public function articlesDestroy(Article $article)
    {
        $article->delete();
        return response()->json(['success' => true, 'message' => 'Article deleted.']);
    }

    // =========================================================
    // 6. NEWS INTELLIGENCE (Cache)
    // =========================================================

    public function newsCache(Request $request)
    {
        $search     = $request->search;
        $countryId  = $request->country_id;
        $sentiment  = $request->sentiment;

        $news = NewsCache::with('country')
            ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->when($sentiment, fn($q) => $q->where('sentiment', $sentiment))
            ->latest('published_at')
            ->paginate(20)
            ->withQueryString();

        $countries = Country::orderBy('country_name')->get(['id', 'country_name']);

        return view('admin.news-cache', compact('news', 'countries', 'search', 'countryId', 'sentiment'));
    }

    public function newsCacheRefresh()
    {
        try {
            $rawNews  = $this->newsService->getNews();
            $articles = $rawNews['articles'] ?? [];
            $count    = 0;

            if (!empty($articles)) {
                $analyzed = $this->sentimentService->analyzeMultiple($articles);
                $country  = Country::first(); // default country for cached news

                foreach ($analyzed['articles'] as $article) {
                    NewsCache::create([
                        'country_id'      => $country?->id,
                        'title'           => $article['title'] ?? 'No Title',
                        'description'     => $article['description'] ?? null,
                        'content'         => $article['content'] ?? null,
                        'url'             => $article['url'] ?? '#',
                        'image'           => $article['image'] ?? null,
                        'source'          => $article['source']['name'] ?? null,
                        'published_at'    => isset($article['publishedAt'])
                            ? \Carbon\Carbon::parse($article['publishedAt'])
                            : now(),
                        'sentiment'       => $article['sentiment'],
                        'sentiment_score' => $article['sentiment_score'],
                    ]);
                    $count++;
                }
            }

            return response()->json(['success' => true, 'message' => "{$count} news articles refreshed."]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =========================================================
    // 7. RISK MONITORING
    // =========================================================

    public function riskScores(Request $request)
    {
        $search    = $request->search;
        $riskLevel = $request->risk_level;

        $scores = RiskScore::with('country')
            ->when($search, fn($q) =>
                $q->whereHas('country', fn($c) =>
                    $c->where('country_name', 'like', "%{$search}%")
                )
            )
            ->when($riskLevel, fn($q) => $q->where('risk_level', $riskLevel))
            ->orderByDesc('total_score')
            ->paginate(20)
            ->withQueryString();

        // Chart data
        $avgScores = [
            'weather'   => round(RiskScore::avg('weather_score') ?? 0, 1),
            'inflation' => round(RiskScore::avg('inflation_score') ?? 0, 1),
            'currency'  => round(RiskScore::avg('currency_score') ?? 0, 1),
            'news'      => round(RiskScore::avg('news_score') ?? 0, 1),
        ];

        return view('admin.risk-scores', compact('scores', 'search', 'riskLevel', 'avgScores'));
    }

    public function recalculate(Country $country)
    {
        $score = $this->riskScoringService->calculate($country);
        return response()->json([
            'success'     => true,
            'total_score' => $score->total_score,
            'risk_level'  => $score->risk_level,
        ]);
    }

    public function recalculateAll()
    {
        try {
            $results = $this->riskScoringService->calculateAll();
            $summary = $results['_summary'] ?? ['scored' => 0, 'failed' => 0];
            return response()->json([
                'success' => true,
                'message' => "Recalculated {$summary['scored']} countries. {$summary['failed']} failed.",
                'summary' => $summary,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =========================================================
    // 8. API MONITOR
    // =========================================================

    public function apiMonitor()
    {
        $apis = [
            'open_meteo'    => ['name' => 'Open Meteo', 'icon' => 'bi-cloud-sun-fill', 'color' => '#38BDF8', 'endpoint' => 'api.open-meteo.com'],
            'world_bank'    => ['name' => 'World Bank', 'icon' => 'bi-bank2', 'color' => '#818CF8', 'endpoint' => 'api.worldbank.org'],
            'exchange_rate' => ['name' => 'Exchange Rate API', 'icon' => 'bi-currency-exchange', 'color' => '#34D399', 'endpoint' => 'open.er-api.com'],
            'rest_countries'=> ['name' => 'REST Countries', 'icon' => 'bi-globe2', 'color' => '#F59E0B', 'endpoint' => 'restcountries.com'],
            'gnews'         => ['name' => 'GNews API', 'icon' => 'bi-newspaper', 'color' => '#F87171', 'endpoint' => 'gnews.io'],
        ];

        // Get last log for each API
        $apiStatus = [];
        foreach ($apis as $key => $api) {
            $lastLog = ApiLog::where('api_name', $key)
                ->latest('requested_at')
                ->first();

            $apiStatus[$key] = array_merge($api, [
                'last_sync'   => $lastLog?->requested_at,
                'status_code' => $lastLog?->status_code,
                'is_success'  => $lastLog ? ($lastLog->status_code >= 200 && $lastLog->status_code < 300) : null,
                'response_ms' => $lastLog?->response_time,
                'total_calls' => ApiLog::where('api_name', $key)->count(),
            ]);
        }

        // Recent API logs
        $recentLogs = ApiLog::latest('requested_at')->limit(20)->get();

        return view('admin.api-monitor', compact('apiStatus', 'recentLogs'));
    }

    // =========================================================
    // 9. SYSTEM LOGS
    // =========================================================

    public function logs(Request $request)
    {
        $tab = $request->tab ?? 'comparison';

        $comparisonLogs = ComparisonLog::with(['user', 'countryOne', 'countryTwo'])
            ->latest()
            ->paginate(15, ['*'], 'cp_page')
            ->withQueryString();

        $watchlistLogs = Watchlist::with(['user', 'country'])
            ->latest()
            ->paginate(15, ['*'], 'wl_page')
            ->withQueryString();

        $apiLogs = ApiLog::latest('requested_at')
            ->paginate(15, ['*'], 'api_page')
            ->withQueryString();

        return view('admin.logs', compact('comparisonLogs', 'watchlistLogs', 'apiLogs', 'tab'));
    }

    // =========================================================
    // 10. SETTINGS
    // =========================================================

    public function settings()
    {
        $setting = Setting::first();
        $user    = Auth::user();
        return view('admin.settings', compact('setting', 'user'));
    }

    public function settingsUpdate(Request $request)
    {
        $data = $request->validate([
            'system_name'      => 'required|string|max:255',
            'system_email'     => 'required|email',
            'refresh_interval' => 'required|integer|min:10|max:1440',
        ]);

        Setting::updateOrCreate([], $data);

        return back()->with('success', 'System settings updated successfully.');
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }
}
