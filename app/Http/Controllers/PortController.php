<?php

namespace App\Http\Controllers;

use App\Models\Port;
use App\Models\Country;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('country_name')->get(['id', 'country_code', 'country_name']);
        $totalPorts = Port::count();

        return view('ports.index', compact('countries', 'totalPorts'));
    }

    public function apiIndex(Request $request)
    {
        $country = $request->get('country');
        $search  = $request->get('search');
        $type    = $request->get('type');

        $ports = Port::with('country')
            ->when($country, fn($q) => $q->whereHas('country', fn($q2) =>
                $q2->where('country_code', strtoupper($country))
            ))
            ->when($search, fn($q) => $q->where('port_name', 'like', "%{$search}%"))
            ->when($type,   fn($q) => $q->where('port_type', $type))
            ->get();

        return response()->json($ports->map(fn($p) => [
            'id'           => $p->id,
            'name'         => $p->port_name,
            'city'         => $p->city,
            'type'         => $p->port_type,
            'status'       => $p->status,
            'lat'          => (float) $p->latitude,
            'lng'          => (float) $p->longitude,
            'country_name' => $p->country?->country_name ?? 'Unknown',
            'country_code' => $p->country?->country_code ?? '',
            'country_url'  => $p->country ? route('countries.show', $p->country->country_code) : '#',
        ]));
    }
}
