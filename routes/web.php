<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\Admin\AdminController;

// ===================================
// PUBLIC ROUTES
// ===================================

Route::get('/', [LandingController::class, 'index']);

Route::get('/login',  [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginProcess'])->name('login.process');

Route::get('/register',  [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'registerStore'])->name('register.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===================================
// AUTHENTICATED USER ROUTES
// ===================================

Route::middleware('auth')->group(function () {

    // ----- Dashboard -----
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ----- Global Map -----
    Route::get('/global-map', [MapController::class, 'index'])->name('global-map');

    // Map AJAX endpoints
    Route::get('/api/map/countries',         [MapController::class, 'countriesData'])->name('api.map.countries');
    Route::get('/api/map/country/{code}',    [MapController::class, 'countryPopup'])->name('api.map.country-popup');
    Route::get('/api/map/ports',             [MapController::class, 'portsData'])->name('api.map.ports');
    Route::get('/api/map/port/{port}/weather', [MapController::class, 'portWeather'])->name('api.map.port-weather');

    // ----- Countries -----
    Route::get('/countries',           [CountryController::class, 'index'])->name('countries');
    Route::get('/countries/{code}',    [CountryController::class, 'show'])->name('countries.show');

    // Country AJAX
    Route::get('/api/country/{code}/risk',   [CountryController::class, 'riskData'])->name('api.country.risk');

    // ----- Comparison -----
    Route::get('/compare',                            [ComparisonController::class, 'index'])->name('compare');
    Route::get('/compare/{codeA}/{codeB}',            [ComparisonController::class, 'show'])->name('compare.show');
    Route::post('/compare',                           [ComparisonController::class, 'compare'])->name('compare.process');

    // ----- News Intelligence -----
    Route::get('/news',                 [NewsController::class, 'index'])->name('news');
    Route::get('/api/news/refresh',     [NewsController::class, 'refresh'])->name('api.news.refresh');

    // ----- Ports Dashboard -----
    Route::get('/ports',                [PortController::class, 'index'])->name('ports');
    Route::get('/api/ports',            [PortController::class, 'apiIndex'])->name('api.ports');

    // ----- Watchlist -----
    Route::get('/watchlist',            [WatchlistController::class, 'index'])->name('watchlist');
    Route::post('/watchlist/toggle',    [WatchlistController::class, 'toggle'])->name('watchlist.toggle');

});

// ===================================
// ADMIN ROUTES (auth + admin role)
// ===================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // ----- Dashboard -----
    Route::get('/',  [AdminController::class, 'index'])->name('dashboard');

    // ----- Users -----
    Route::get('/users',                    [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/toggle',    [AdminController::class, 'usersToggle'])->name('users.toggle');
    Route::delete('/users/{user}',          [AdminController::class, 'usersDestroy'])->name('users.destroy');

    // ----- Countries -----
    Route::get('/countries',                [AdminController::class, 'countries'])->name('countries');
    Route::post('/countries/refresh',       [AdminController::class, 'countriesRefresh'])->name('countries.refresh');
    Route::post('/economic/refresh',        [AdminController::class, 'economicRefresh'])->name('economic.refresh');
    Route::post('/weather/refresh',         [AdminController::class, 'weatherRefresh'])->name('weather.refresh');
    Route::post('/currency/refresh',        [AdminController::class, 'currencyRefresh'])->name('currency.refresh');

    // ----- Ports -----
    Route::get('/ports',                    [AdminController::class, 'ports'])->name('ports');
    Route::post('/ports',                   [AdminController::class, 'portsStore'])->name('ports.store');
    Route::put('/ports/{port}',             [AdminController::class, 'portsUpdate'])->name('ports.update');
    Route::delete('/ports/{port}',          [AdminController::class, 'portsDestroy'])->name('ports.destroy');
    Route::get('/ports/export',             [AdminController::class, 'portsExport'])->name('ports.export');
    Route::post('/ports/import',            [AdminController::class, 'portsImport'])->name('ports.import');

    // ----- Articles -----
    Route::get('/articles',                 [AdminController::class, 'articles'])->name('articles');
    Route::get('/articles/create',          [AdminController::class, 'articlesCreate'])->name('articles.create');
    Route::post('/articles',                [AdminController::class, 'articlesStore'])->name('articles.store');
    Route::get('/articles/{article}/edit',  [AdminController::class, 'articlesEdit'])->name('articles.edit');
    Route::put('/articles/{article}',       [AdminController::class, 'articlesUpdate'])->name('articles.update');
    Route::delete('/articles/{article}',    [AdminController::class, 'articlesDestroy'])->name('articles.destroy');

    // ----- News Cache -----
    Route::get('/news-cache',               [AdminController::class, 'newsCache'])->name('news-cache');
    Route::post('/news-cache/refresh',      [AdminController::class, 'newsCacheRefresh'])->name('news-cache.refresh');

    // ----- Risk Scores -----
    Route::get('/risk-scores',                              [AdminController::class, 'riskScores'])->name('risk-scores');
    Route::post('/risk-scores/recalculate/{country}',       [AdminController::class, 'recalculate'])->name('risk-scores.recalculate');
    Route::post('/risk-scores/recalculate-all',             [AdminController::class, 'recalculateAll'])->name('risk-scores.recalculate-all');

    // ----- API Monitor -----
    Route::get('/api-monitor',              [AdminController::class, 'apiMonitor'])->name('api-monitor');

    // ----- Logs -----
    Route::get('/logs',                     [AdminController::class, 'logs'])->name('logs');

    // ----- Settings -----
    Route::get('/settings',                 [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings',                [AdminController::class, 'settingsUpdate'])->name('settings.update');
    Route::post('/settings/password',       [AdminController::class, 'passwordUpdate'])->name('settings.password');
    Route::post('/settings/profile',        [AdminController::class, 'profileUpdate'])->name('settings.profile');

});