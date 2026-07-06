<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EconomicService
{
    private $baseUrl = "https://api.worldbank.org/v2";

    public function getIndicator($country,$indicator)
    {
        $response = Http::timeout(30)
            ->retry(3,1000)
            ->get("{$this->baseUrl}/country/{$country}/indicator/{$indicator}",[
                'format'=>'json',
                'per_page'=>1
            ]);

        if(!$response->successful()){
            return null;
        }

        $data = $response->json();

        if(!isset($data[1][0]['value'])){
            return null;
        }

        return $data[1][0]['value'];
    }
}