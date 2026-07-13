<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;
use Illuminate\Support\Facades\Cache;

class SentimentService
{
    private array $positiveWords = [];
    private array $negativeWords = [];

    public function __construct()
    {
        $this->loadLexicon();
    }

    // ======================================
    // LOAD LEXICON FROM DB (CACHED 1 HOUR)
    // ======================================

    private function loadLexicon(): void
    {
        $this->positiveWords = Cache::remember('lexicon.positive', 3600, function () {
            return PositiveWord::pluck('word')->map(fn($w) => strtolower($w))->toArray();
        });

        $this->negativeWords = Cache::remember('lexicon.negative', 3600, function () {
            return NegativeWord::pluck('word')->map(fn($w) => strtolower($w))->toArray();
        });

        // Fallback built-in lexicon if DB is empty
        if (empty($this->positiveWords)) {
            $this->positiveWords = $this->getBuiltInPositive();
        }
        if (empty($this->negativeWords)) {
            $this->negativeWords = $this->getBuiltInNegative();
        }
    }

    // ======================================
    // ANALYZE TEXT
    // ======================================

    /**
     * Analyze a text and return sentiment label + score.
     *
     * @param string $text
     * @return array{sentiment: string, score: float, pos_count: int, neg_count: int}
     */
    public function analyze(string $text): array
    {
        $words = $this->tokenize($text);
        $posCount = 0;
        $negCount = 0;

        foreach ($words as $word) {
            if (in_array($word, $this->positiveWords, true)) {
                $posCount++;
            } elseif (in_array($word, $this->negativeWords, true)) {
                $negCount++;
            }
        }

        $total = $posCount + $negCount;
        $score = $total > 0 ? ($posCount - $negCount) / $total : 0.0;

        $sentiment = 'Neutral';
        if ($score > 0.1)  $sentiment = 'Positive';
        if ($score < -0.1) $sentiment = 'Negative';

        return [
            'sentiment'  => $sentiment,
            'score'      => round($score, 4),
            'pos_count'  => $posCount,
            'neg_count'  => $negCount,
        ];
    }

    /**
     * Analyze multiple articles and return aggregate stats.
     *
     * @param array $articles  Each item: ['title'=>'...', 'description'=>'...']
     * @return array
     */
    public function analyzeMultiple(array $articles): array
    {
        $counts = ['Positive' => 0, 'Neutral' => 0, 'Negative' => 0];
        $results = [];

        foreach ($articles as $article) {
            $text   = ($article['title'] ?? '') . ' ' . ($article['description'] ?? '');
            $result = $this->analyze($text);
            $counts[$result['sentiment']]++;
            $results[] = array_merge($article, [
                'sentiment'       => $result['sentiment'],
                'sentiment_score' => $result['score'],
            ]);
        }

        $total = count($articles) ?: 1;

        return [
            'articles'   => $results,
            'counts'     => $counts,
            'positive_pct' => round($counts['Positive'] / $total * 100, 1),
            'neutral_pct'  => round($counts['Neutral']  / $total * 100, 1),
            'negative_pct' => round($counts['Negative'] / $total * 100, 1),
            // Overall news sentiment score: 0–100 (higher = worse for risk)
            'risk_score'   => round((($counts['Negative'] * 2 + $counts['Neutral']) / ($total * 2)) * 100, 2),
        ];
    }

    // ======================================
    // HELPERS
    // ======================================

    private function tokenize(string $text): array
    {
        $text  = strtolower($text);
        $text  = preg_replace('/[^a-z\s]/', ' ', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        return $words;
    }

    // ======================================
    // BUILT-IN LEXICONS (FALLBACK)
    // ======================================

    private function getBuiltInPositive(): array
    {
        return [
            'growth', 'stable', 'increase', 'boost', 'record', 'strong', 'positive',
            'improve', 'recovery', 'rise', 'gain', 'success', 'profit', 'expand',
            'innovation', 'agreement', 'deal', 'partner', 'cooperation', 'progress',
            'surge', 'thrive', 'flourish', 'advance', 'upbeat', 'optimistic', 'benefit',
            'opportunity', 'resilient', 'surplus', 'achieve', 'excellent', 'good',
            'favorable', 'secure', 'reliable', 'efficient', 'trusted', 'robust',
            'upgrade', 'breakthrough', 'accelerate', 'outperform', 'record-high',
        ];
    }

    private function getBuiltInNegative(): array
    {
        return [
            'crisis', 'war', 'conflict', 'sanction', 'tariff', 'disruption', 'shortage',
            'risk', 'danger', 'threat', 'decline', 'fall', 'recession', 'inflation',
            'flood', 'earthquake', 'disaster', 'storm', 'hurricane', 'typhoon', 'drought',
            'strike', 'protest', 'riot', 'political', 'instability', 'corruption',
            'debt', 'default', 'bankrupt', 'loss', 'drop', 'decrease', 'plunge',
            'collapse', 'fail', 'ban', 'restriction', 'blockade', 'embargo', 'freeze',
            'attack', 'violence', 'terror', 'bomb', 'missile', 'military', 'tension',
            'delay', 'halt', 'suspend', 'cancel', 'penalty', 'fine', 'lawsuit',
            'contamination', 'pollution', 'accident', 'crash', 'shutdown',
        ];
    }
}
