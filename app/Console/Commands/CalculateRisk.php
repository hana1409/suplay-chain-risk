<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Services\RiskScoringService;

class CalculateRisk extends Command
{
    protected $signature = 'risk:calculate-all
                            {--chunk=50 : Process countries in chunks of this size}
                            {--country= : Only calculate for a specific country code}';

    protected $description = 'Calculate and store risk scores for every country in the database';

    public function handle(RiskScoringService $riskScoringService): int
    {
        $countryCode = $this->option('country');
        $chunkSize   = (int) $this->option('chunk');

        // ── Single country mode ─────────────────────────────
        if ($countryCode) {
            $country = Country::where('country_code', strtoupper($countryCode))->first();
            if (!$country) {
                $this->error("Country not found: {$countryCode}");
                return self::FAILURE;
            }
            try {
                $score = $riskScoringService->calculate($country);
                $this->info("✓ {$country->country_name} — Score: {$score->total_score} ({$score->risk_level})");
                return self::SUCCESS;
            } catch (\Throwable $e) {
                $this->error("✗ {$country->country_name} — {$e->getMessage()}");
                return self::FAILURE;
            }
        }

        // ── Batch mode: all countries ───────────────────────
        $total   = Country::count();
        $scored  = 0;
        $failed  = 0;

        $this->info("=================================================");
        $this->info(" Risk Score Calculation — {$total} countries");
        $this->info("=================================================");

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->start();

        Country::with([
            'weatherCache',
            'economicCache',
            'currencyCache',
            'newsCaches',
        ])
        ->chunkById($chunkSize, function ($countries) use (
            $riskScoringService, &$scored, &$failed, $bar
        ) {
            foreach ($countries as $country) {
                $bar->setMessage($country->country_name);
                try {
                    $riskScoringService->calculate($country);
                    $scored++;
                } catch (\Throwable $e) {
                    $failed++;
                    // Log but continue — one failure must not stop the batch
                    $this->newLine();
                    $this->warn("  ✗ {$country->country_name}: {$e->getMessage()}");
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("=================================================");
        $this->info(" Scoring Complete");
        $this->info("   Total countries : {$total}");
        $this->info("   Scored          : {$scored}");
        if ($failed > 0) {
            $this->warn("   Failed          : {$failed}");
        } else {
            $this->info("   Failed          : 0");
        }
        $this->info("=================================================");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
