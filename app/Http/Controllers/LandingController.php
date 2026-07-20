<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing.index');
    }

    public function features()
    {
        return view('landing.features');
    }

    public function countries()
    {
        $countries = Country::with(['riskScore', 'weatherCache'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('country_name')
            ->paginate(20);

        return view('landing.countries', compact('countries'));
    }

    public function about()
    {
        return view('landing.about');
    }

    public function pricing()
    {
        return view('landing.pricing');
    }

    public function contact()
    {
        return view('landing.contact');
    }
}