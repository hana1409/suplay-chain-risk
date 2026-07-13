<?php

namespace App\Http\Controllers;

use App\Models\NewsCache;
use App\Models\Country;
use App\Services\NewsService;
use App\Services\SentimentService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NewsController extends Controller
{
    public function __construct(
        private readonly NewsService       $newsService,
        private readonly SentimentService  $sentimentService
    ) {}

    public function index(Request $request)
    {
        $category = $request->get('category', 'all');

        $query = NewsCache::with('country')->latest();

        if ($category !== 'all') {
            $keywords = $this->categoryKeywords($category);
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('title', 'like', "%{$kw}%");
                }
            });
        }

        $news = $query->paginate(12)->withQueryString();

        // Sentiment summary
        $sentimentCounts = NewsCache::selectRaw("sentiment, COUNT(*) as count")
            ->groupBy('sentiment')
            ->pluck('count', 'sentiment')
            ->toArray();

        $totalNews = array_sum($sentimentCounts);

        return view('news.index', compact('news', 'category', 'sentimentCounts', 'totalNews'));
    }

    public function refresh()
    {
        $raw      = $this->newsService->getNews();
        $articles = $raw['articles'] ?? [];

        if (empty($articles)) {
            return response()->json(['message' => 'No articles fetched.'], 200);
        }

        $analyzed = $this->sentimentService->analyzeMultiple($articles);
        $created  = 0;

        foreach ($analyzed['articles'] as $article) {
            $exists = NewsCache::where('url', $article['url'] ?? '')->exists();
            if (!$exists) {
                NewsCache::create([
                    'country_id'      => null,
                    'title'           => $article['title'] ?? 'No Title',
                    'description'     => $article['description'] ?? null,
                    'url'             => $article['url'] ?? '#',
                    'image'           => $article['image'] ?? null,
                    'source'          => $article['source']['name'] ?? null,
                    'published_at'    => isset($article['publishedAt'])
                                        ? Carbon::parse($article['publishedAt'])
                                        : now(),
                    'sentiment'       => $article['sentiment'],
                    'sentiment_score' => $article['sentiment_score'],
                ]);
                $created++;
            }
        }

        return response()->json([
            'message' => "{$created} new articles cached.",
            'total'   => count($articles),
        ]);
    }

    private function categoryKeywords(string $category): array
    {
        return match ($category) {
            'shipping'  => ['ship', 'shipping', 'vessel', 'maritime', 'port', 'cargo', 'freight', 'container'],
            'trade'     => ['trade', 'export', 'import', 'tariff', 'wto', 'customs', 'supply chain'],
            'economy'   => ['economy', 'gdp', 'inflation', 'recession', 'growth', 'market', 'bank', 'financial'],
            'logistics' => ['logistics', 'supply', 'warehouse', 'distribution', 'transport', 'delivery'],
            'political' => ['war', 'sanction', 'political', 'government', 'conflict', 'election', 'tension'],
            default     => [],
        };
    }
}
