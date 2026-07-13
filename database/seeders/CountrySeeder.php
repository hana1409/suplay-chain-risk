<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Fetching countries from REST Countries API...');

        $response = Http::timeout(30)->get('https://restcountries.com/v3.1/all?fields=cca2,cca3,name,capital,region,subregion,currencies,languages,population,latlng,timezones,flags,flag');

        if (!$response->successful()) {
            $this->command->error('Failed to fetch countries. Using fallback...');
            return;
        }

        $countries = $response->json();
        $count     = 0;
        $skipped   = 0;

        foreach ($countries as $data) {
            $code  = $data['cca2'] ?? null;
            $code3 = $data['cca3'] ?? null;

            if (!$code || !$code3) {
                $skipped++;
                continue;
            }

            // Parse currency
            $currencyCode = null;
            $currencyName = null;
            if (!empty($data['currencies'])) {
                $firstCurrency = array_values($data['currencies'])[0];
                $currencyCode  = array_key_first($data['currencies']);
                $currencyName  = $firstCurrency['name'] ?? null;
            }

            // Parse language
            $language = null;
            if (!empty($data['languages'])) {
                $language = implode(', ', array_values($data['languages']));
            }

            // Parse lat/lng
            $lat = $data['latlng'][0] ?? null;
            $lng = $data['latlng'][1] ?? null;

            // Parse timezone
            $timezone = $data['timezones'][0] ?? null;

            // Parse capital
            $capital = !empty($data['capital']) ? $data['capital'][0] : null;

            Country::updateOrCreate(
                ['country_code' => $code],
                [
                    'country_code3' => $code3,
                    'country_name'  => $data['name']['common'] ?? $code,
                    'official_name' => $data['name']['official'] ?? null,
                    'capital'       => $capital,
                    'region'        => $data['region'] ?? null,
                    'subregion'     => $data['subregion'] ?? null,
                    'currency_code' => $currencyCode,
                    'currency_name' => $currencyName,
                    'language'      => $language ? substr($language, 0, 255) : null,
                    'population'    => $data['population'] ?? null,
                    'latitude'      => $lat,
                    'longitude'     => $lng,
                    'timezone'      => $timezone,
                    'flag'          => $data['flag'] ?? null,
                    'flag_png'      => $data['flags']['png'] ?? null,
                    'flag_svg'      => $data['flags']['svg'] ?? null,
                ]
            );

            $count++;
        }

        $this->command->info("✓ Seeded {$count} countries. Skipped: {$skipped}");
    }
}
