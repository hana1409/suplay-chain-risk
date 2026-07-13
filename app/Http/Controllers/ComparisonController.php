<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\ComparisonLog;
use App\Models\RiskScore;
use App\Models\EconomicCache;
use App\Models\WeatherCache;
use App\Models\CurrencyCache;
use App\Models\NewsCache;
use App\Services\RiskScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComparisonController extends Controller
{
    public function __construct(
        private readonly RiskScoringService $riskScoringService
    ) {}

    public function index(Request $request)
    {
        $countries = Country::orderBy('country_name')->get(['id', 'country_code', 'country_name', 'flag']);
        $preselected = $request->get('a');

        return view('comparison.index', compact('countries', 'preselected'));
    }

    public function show(string $codeA, string $codeB)
    {
        $countryA = Country::where('country_code', strtoupper($codeA))->firstOrFail();
        $countryB = Country::where('country_code', strtoupper($codeB))->firstOrFail();

        $dataA = $this->gatherData($countryA);
        $dataB = $this->gatherData($countryB);

        $recommendationA = $this->riskScoringService->getRecommendation($dataA['risk'], $countryA);
        $recommendationB = $this->riskScoringService->getRecommendation($dataB['risk'], $countryB);

        // Log comparison
        if (Auth::check()) {
            ComparisonLog::create([
                'user_id'        => Auth::id(),
                'country_one_id' => $countryA->id,
                'country_two_id' => $countryB->id,
            ]);
        }

        return view('comparison.show', compact(
            'countryA', 'countryB',
            'dataA',    'dataB',
            'recommendationA', 'recommendationB'
        ));
    }

    public function compare(Request $request)
    {
        $request->validate([
            'country_a' => 'required|string|size:2',
            'country_b' => 'required|string|size:2',
        ]);

        return redirect()->route('compare.show', [
            'codeA' => strtoupper($request->country_a),
            'codeB' => strtoupper($request->country_b),
        ]);
    }

    private function gatherData(Country $country): array
    {
        $risk     = RiskScore::where('country_id', $country->id)->first()
                    ?? $this->riskScoringService->calculate($country);
        $economic = EconomicCache::where('country_id', $country->id)->latest('year')->first();
        $weather  = WeatherCache::where('country_id', $country->id)->latest()->first();
        $currency = CurrencyCache::where('country_id', $country->id)->latest()->first();
        $newsCount= NewsCache::where('country_id', $country->id)
                      ->selectRaw("sentiment, COUNT(*) as count")
                      ->groupBy('sentiment')
                      ->pluck('count', 'sentiment')
                      ->toArray();

        return compact('risk', 'economic', 'weather', 'currency', 'newsCount');
    }
}
