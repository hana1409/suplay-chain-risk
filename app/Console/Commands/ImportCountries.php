<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Services\CountryService;

class ImportCountries extends Command
{
    protected $signature = 'countries:import';

    protected $description = 'Import countries from GitHub JSON';

    public function handle(CountryService $service)
    {
        $countries = $service->getCountries();

        foreach ($countries as $country) {

            // Currency
            $currencyCode = null;
            $currencyName = null;

            if (!empty($country['currencies'])) {

                $currencyCode = array_key_first($country['currencies']);

                $currencyName = $country['currencies'][$currencyCode]['name'] ?? null;
            }

            // Language
            $language = null;

            if (!empty($country['languages'])) {

                $language = implode(', ', array_values($country['languages']));
            }

            Country::updateOrCreate(

                [
                    'country_code' => $country['cca2']
                ],

                [

                    'country_code3' => $country['cca3'],

                    'country_name' => $country['name']['common'] ?? null,

                    'official_name' => $country['name']['official'] ?? null,

                    'capital' => $country['capital'][0] ?? null,

                    'region' => $country['region'] ?? null,

                    'subregion' => $country['subregion'] ?? null,

                    'currency_code' => $currencyCode,

                    'currency_name' => $currencyName,

                    'language' => $language,

                    'population' => $country['population'] ?? 0,

                    'latitude' => $country['latlng'][0] ?? null,

                    'longitude' => $country['latlng'][1] ?? null,

                    'timezone' => $country['timezones'][0] ?? null,

                    'flag' => $country['flag'] ?? null,

                    'flag_png' => $country['flags']['png'] ?? null,

                    'flag_svg' => $country['flags']['svg'] ?? null,

                ]

            );

            $this->info("Imported : ".$country['name']['common']);

        }

        $this->info("================================");

        $this->info("Countries Imported Successfully!");

        $this->info("================================");
    }
}