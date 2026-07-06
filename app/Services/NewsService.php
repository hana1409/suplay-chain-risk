<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsService
{
    public function getNews()
    {
        $query = "logistics OR shipping OR trade OR economy";

        $response = Http::timeout(20)
            ->retry(3,1000)
            ->get('https://gnews.io/api/v4/search',[
                'q'=>$query,
                'lang'=>'en',
                'max'=>10,
                'apikey'=>env('GNEWS_API_KEY')
            ]);

        if(!$response->successful()){
            return null;
        }

        return $response->json();
    }
}