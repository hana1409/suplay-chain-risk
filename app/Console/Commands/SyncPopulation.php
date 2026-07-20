<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\EconomicCache;
use App\Services\EconomicService;
use Illuminate\Console\Command;

class SyncPopulation extends Command
{
    protected $signature = 'sync:population';

    protected $description = 'Sync population data from World Bank API to economic_caches';

    public function handle(EconomicService $economicService)
    {
        $this->info('Syncing population data from World Bank API...');
        $this->newLine();

        $countries = Country::whereNotNull('country_code3')->get();
        $bar = $this->output->createProgressBar($countries->count());

        $updated = 0;
        $skipped = 0;
        $year = now()->year - 1; // Latest available year

        foreach ($countries as $country) {
            // SP.POP.TOTL = Total Population indicator from World Bank
            $population = $economicService->getIndicator($country->country_code3, 'SP.POP.TOTL');
            
            if ($population !== null && $population > 0) {
                EconomicCache::updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'year' => $year
                    ],
                    [
                        'population' => (int)$population
                    ]
                );
                $updated++;
            } else {
                $skipped++;
            }
            
            $bar->advance();
            usleep(100000); // 0.1 second delay to avoid rate limiting
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("✓ Synced population for {$updated} countries");
        if ($skipped > 0) {
            $this->warn("⚠ Skipped {$skipped} countries (no data from API)");
        }
        
        return 0;
    }
}
