<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Country;
use App\Models\Port;
use App\Models\Article;
use App\Models\NewsCache;
use App\Models\RiskScore;
use App\Services\RiskScoringService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private readonly RiskScoringService $riskScoringService
    ) {}

    public function index()
    {
        $stats = [
            'users'      => User::count(),
            'countries'  => Country::count(),
            'ports'      => Port::count(),
            'news_cache' => NewsCache::count(),
            'risk_scores'=> RiskScore::count(),
            'articles'   => Article::count(),
        ];

        $recentUsers = User::latest()->limit(5)->get();
        $topRisk = RiskScore::with('country')
            ->orderByDesc('total_score')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'topRisk'));
    }

    public function users()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function ports()
    {
        $ports     = Port::with('country')->paginate(20);
        $countries = Country::orderBy('country_name')->get(['id', 'country_name']);
        return view('admin.ports', compact('ports', 'countries'));
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
        Port::create($data);
        return back()->with('success', 'Port created successfully.');
    }

    public function portsUpdate(Request $request, Port $port)
    {
        $data = $request->validate([
            'port_name' => 'required|string|max:255',
            'city'      => 'nullable|string|max:255',
            'port_type' => 'required|string',
            'status'    => 'required|in:Active,Inactive',
        ]);
        $port->update($data);
        return back()->with('success', 'Port updated.');
    }

    public function portsDestroy(Port $port)
    {
        $port->delete();
        return back()->with('success', 'Port deleted.');
    }

    public function articles()
    {
        $articles = Article::with('user')->latest()->paginate(15);
        return view('admin.articles', compact('articles'));
    }

    public function newsCache()
    {
        $news = NewsCache::with('country')->latest()->paginate(20);
        return view('admin.news-cache', compact('news'));
    }

    public function riskScores()
    {
        $scores = RiskScore::with('country')->orderByDesc('total_score')->paginate(20);
        return view('admin.risk-scores', compact('scores'));
    }

    public function recalculate(Country $country)
    {
        $score = $this->riskScoringService->calculate($country);
        return response()->json([
            'success'    => true,
            'total_score'=> $score->total_score,
            'risk_level' => $score->risk_level,
        ]);
    }
}
