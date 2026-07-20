<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Country;
use App\Services\EconomicService;

echo "Updating population data from World Bank API...\n\n";

$economicService = new EconomicService();
$countries = Country::whereNotNull('country_code3')->get();

$updated = 0;
$skipped = 0;

foreach ($countries as $country) {
    // SP.POP.TOTL = Total Population indicator from World Bank
    $population = $economicService->getIndicator($country->country_code3, 'SP.POP.TOTL');
    
    if ($population !== null && $population > 0) {
        $country->update(['population' => (int)$population]);
        echo sprintf("%-3s %-30s %15s\n", 
            $country->country_code, 
            substr($country->country_name, 0, 30), 
            number_format($population)
        );
        $updated++;
    } else {
        echo sprintf("%-3s %-30s %15s (no data)\n", 
            $country->country_code, 
            substr($country->country_name, 0, 30), 
            'N/A'
        );
        $skipped++;
    }
    
    // Small delay to avoid rate limiting
    usleep(100000); // 0.1 second
}

echo "\n================================\n";
echo "Updated: $updated countries\n";
echo "Skipped: $skipped countries (no data from API)\n";
echo "================================\n";
