<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

echo "Testing GitHub API...\n\n";

$url = 'https://raw.githubusercontent.com/mledoze/countries/master/countries.json';
$response = Http::timeout(10)->get($url);

if ($response->successful()) {
    $countries = $response->json();
    echo "Total countries from API: " . count($countries) . "\n\n";
    
    // Check first country
    $first = $countries[0];
    echo "First country sample:\n";
    echo "Name: " . ($first['name']['common'] ?? 'N/A') . "\n";
    echo "Code: " . ($first['cca2'] ?? 'N/A') . "\n";
    echo "Population: " . ($first['population'] ?? 'N/A') . "\n\n";
    
    // Check Indonesia
    $indonesia = array_values(array_filter($countries, fn($c) => $c['cca2'] === 'ID'));
    if (!empty($indonesia)) {
        echo "Indonesia data:\n";
        echo "Name: " . ($indonesia[0]['name']['common'] ?? 'N/A') . "\n";
        echo "Population: " . ($indonesia[0]['population'] ?? 'N/A') . "\n";
        echo "Has population key: " . (isset($indonesia[0]['population']) ? 'YES' : 'NO') . "\n";
    }
} else {
    echo "API request failed\n";
}
