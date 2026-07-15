<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run Admin seeder first (ensures roles + admin account exist)
        $this->call(AdminSeeder::class);

        // Other seeders
        // $this->call(CountrySeeder::class);
        // $this->call(SentimentWordsSeeder::class);
    }
}
