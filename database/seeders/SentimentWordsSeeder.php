<?php

namespace Database\Seeders;

use App\Models\PositiveWord;
use App\Models\NegativeWord;
use Illuminate\Database\Seeder;

class SentimentWordsSeeder extends Seeder
{
    public function run(): void
    {
        $positiveWords = [
            'growth', 'stable', 'increase', 'boost', 'record', 'strong', 'positive',
            'improve', 'recovery', 'rise', 'gain', 'success', 'profit', 'expand',
            'innovation', 'agreement', 'deal', 'partner', 'cooperation', 'progress',
            'surge', 'thrive', 'flourish', 'advance', 'upbeat', 'optimistic', 'benefit',
            'opportunity', 'resilient', 'surplus', 'achieve', 'excellent', 'good',
            'favorable', 'secure', 'reliable', 'efficient', 'trusted', 'robust',
            'upgrade', 'breakthrough', 'accelerate', 'outperform', 'high', 'peak',
            'open', 'reform', 'invest', 'confident', 'recover', 'rebound', 'boost',
            'prosperous', 'successful', 'thriving', 'momentum', 'solid', 'healthy',
            'transparent', 'safe', 'free', 'fair', 'peace', 'stability', 'friendly',
            'export', 'import', 'market', 'trade', 'alliance', 'treaty', 'accord',
        ];

        $negativeWords = [
            'crisis', 'war', 'conflict', 'sanction', 'tariff', 'disruption', 'shortage',
            'risk', 'danger', 'threat', 'decline', 'fall', 'recession', 'inflation',
            'flood', 'earthquake', 'disaster', 'storm', 'hurricane', 'typhoon', 'drought',
            'strike', 'protest', 'riot', 'political', 'instability', 'corruption',
            'debt', 'default', 'bankrupt', 'loss', 'drop', 'decrease', 'plunge',
            'collapse', 'fail', 'ban', 'restriction', 'blockade', 'embargo', 'freeze',
            'attack', 'violence', 'terror', 'bomb', 'missile', 'military', 'tension',
            'delay', 'halt', 'suspend', 'cancel', 'penalty', 'fine', 'lawsuit',
            'contamination', 'pollution', 'accident', 'crash', 'shutdown', 'closure',
            'exodus', 'refugee', 'displacement', 'famine', 'poverty', 'inequality',
            'corrupt', 'fraud', 'scandal', 'probe', 'investigation', 'arrest', 'seized',
            'hack', 'cyber', 'breach', 'leak', 'spill', 'toxic', 'chemical', 'nuclear',
            'blockage', 'supply', 'chain', 'backlog', 'congestion', 'bottleneck', 'slowdown',
            'unemployment', 'layoff', 'closure', 'downturn', 'weak', 'volatile', 'uncertainty',
        ];

        // Upsert words (handle unique constraint)
        foreach ($positiveWords as $word) {
            PositiveWord::firstOrCreate(['word' => $word]);
        }

        foreach ($negativeWords as $word) {
            NegativeWord::firstOrCreate(['word' => $word]);
        }

        $this->command->info('✓ Seeded ' . count($positiveWords) . ' positive words and ' . count($negativeWords) . ' negative words.');
    }
}
