<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\CurrencyCache;
use App\Services\CurrencyService;

class SyncCurrency extends Command
{
    protected $signature = 'currency:sync';

    protected $description = 'Sync exchange rates';

    public function handle(CurrencyService $service)
    {
        $data = $service->getRates();

        if (!$data || !isset($data['rates'])) {

            $this->error('Failed to retrieve exchange rates.');

            return;
        }

        $rates = $data['rates'];

        $countries = Country::all();

        foreach ($countries as $country) {

            try {

                if (!$country->currency_code) {

                    $this->warn("Skip {$country->country_name}");

                    continue;
                }

                if (!isset($rates[$country->currency_code])) {

                    $this->warn("Currency {$country->currency_code} not found");

                    continue;
                }

                CurrencyCache::updateOrCreate(

                    [
                        'country_id' => $country->id
                    ],

                    [

                        'base_currency' => 'USD',

                        'target_currency' => $country->currency_code,

                        'exchange_rate' => $rates[$country->currency_code],

                        'rate_date' => now()

                    ]

                );

                $this->info("Synced : {$country->country_name}");

            } catch (\Exception $e) {

                $this->error("Failed : {$country->country_name}");

                continue;

            }

        }

        $this->info("==============================");

        $this->info("Currency Sync Completed!");

        $this->info("==============================");

    }
}