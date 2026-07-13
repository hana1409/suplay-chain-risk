<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    public function index()
    {
        $watchlists = Watchlist::with(['country.riskScore', 'country.economicCache', 'country.weatherCache'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('watchlist.index', compact('watchlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
        ]);

        $existing = Watchlist::where('user_id', Auth::id())
                             ->where('country_id', $request->country_id)
                             ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['action' => 'removed', 'message' => 'Removed from watchlist']);
        }

        Watchlist::create([
            'user_id'    => Auth::id(),
            'country_id' => $request->country_id,
        ]);

        return response()->json(['action' => 'added', 'message' => 'Added to watchlist']);
    }
}
