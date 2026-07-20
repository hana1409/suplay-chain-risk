<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Country;
use Illuminate\Support\Facades\Http;

echo "Updating population data from REST Countries API...\n\n";

$response = Http::timeout(30)->get('https://restcountries.com/v3.1/all?fields=cca2,population');

if (!$response->successful()) {
    echo "Failed to fetch data from REST Countries API\n";
    exit(1);
}

$countries = $response->json();
$updated = 0;
$skipped = 0;

foreach ($countries as $data) {
    $code = $data['cca2'] ?? null;
    $population = $data['population'] ?? null;
    
    if (!$code) {
        $skipped++;
        continue;
    }
    
    $country = Country::where('country_code', $code)->first();
    
    if ($country) {
        $country->update(['population' => $population]);
        echo sprintf("Updated %-30s population: %s\n", $country->country_name, number_format($population ?? 0));
        $updated++;
    } else {
        $skipped++;
    }
}

echo "\n================================\n";
echo "Updated: $updated countries\n";
echo "Skipped: $skipped countries\n";
echo "================================\n";
