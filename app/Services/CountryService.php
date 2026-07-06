<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CountryService
{
    public function getCountries()
    {
        $url = 'https://raw.githubusercontent.com/mledoze/countries/master/countries.json';

        $response = Http::get($url);

        if (!$response->successful()) {
            return [];
        }

        return $response->json();
    }
}