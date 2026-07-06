<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\EconomicCache;
use App\Services\EconomicService;

class SyncEconomic extends Command
{
    protected $signature = 'economic:sync';

    protected $description = 'Sync World Bank Economic Data';

    public function handle(EconomicService $service)
    {
        $countries = Country::all();

        foreach($countries as $country){

            try{

                $code = strtolower($country->country_code);

                $gdp = $service->getIndicator(
                    $code,
                    'NY.GDP.MKTP.CD'
                );

                $inflation = $service->getIndicator(
                    $code,
                    'FP.CPI.TOTL.ZG'
                );

                $population = $service->getIndicator(
                    $code,
                    'SP.POP.TOTL'
                );

                $exports = $service->getIndicator(
                    $code,
                    'NE.EXP.GNFS.CD'
                );

                $imports = $service->getIndicator(
                    $code,
                    'NE.IMP.GNFS.CD'
                );

                EconomicCache::updateOrCreate(

                    [
                        'country_id'=>$country->id
                    ],

                    [

                        'gdp'=>$gdp,

                        'inflation'=>$inflation,

                        'exports'=>$exports,

                        'imports'=>$imports,

                        'population'=>$population,

                        'year'=>date('Y')

                    ]

                );

                $this->info("Synced : ".$country->country_name);

            }

            catch(\Exception $e){

                $this->error("Failed : ".$country->country_name);

                $this->error($e->getMessage());

            }

        }

        $this->info("==============================");

        $this->info("Economic Sync Completed!");

        $this->info("==============================");
    }
}