<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Country;

echo "Checking population data...\n\n";

// Check total countries
$total = Country::count();
echo "Total countries: $total\n";

// Check countries with population > 0
$withPop = Country::where('population', '>', 0)->count();
echo "Countries with population > 0: $withPop\n";

// Check countries with population = 0
$zeroPop = Country::where('population', 0)->count();
echo "Countries with population = 0: $zeroPop\n";

// Check NULL
$nullPop = Country::whereNull('population')->count();
echo "Countries with NULL population: $nullPop\n\n";

// Sample data
echo "Sample countries:\n";
$samples = Country::whereIn('country_code', ['ID', 'US', 'AZ', 'BD', 'BE'])->get(['country_code', 'country_name', 'population']);
foreach ($samples as $c) {
    echo sprintf("%-3s %-20s %s\n", $c->country_code, $c->country_name, number_format($c->population ?? 0));
}
