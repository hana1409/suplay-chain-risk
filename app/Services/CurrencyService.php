<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyService
{
    public function getRates()
    {
        $response = Http::timeout(20)
            ->retry(3,1000)
            ->get('https://open.er-api.com/v6/latest/USD');

        if(!$response->successful()){
            return null;
        }

        return $response->json();
    }
}