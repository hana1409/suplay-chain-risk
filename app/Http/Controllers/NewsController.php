<?php

namespace App\Http\Controllers;

use App\Models\NewsCache;
use App\Models\Article;
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

        // Gabungkan NewsCache dan Articles
        $newsCache = NewsCache::with('country')
            ->when($category !== 'all', function ($q) use ($category) {
                $keywords = $this->categoryKeywords($category);
                $q->where(function ($query) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $query->orWhere('title', 'like', "%{$kw}%");
                    }
                });
            })
            ->get()
            ->map(function ($item) {
                return [
                    'type'         => 'news',
                    'title'        => $item->title,
                    'description'  => $item->description,
                    'content'      => $item->content,
                    'image'        => $item->image,
                    'url'          => $item->url,
                    'source'       => $item->source,
                    'country'      => $item->country,
                    'published_at' => $item->published_at,
                    'sentiment'    => $item->sentiment,
                    'created_at'   => $item->created_at,
                ];
            });

        // Ambil artikel yang Published
        $articles = Article::where('status', 'Published')
            ->when($category !== 'all', function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->get()
            ->map(function ($item) {
                return [
                    'type'         => 'article',
                    'title'        => $item->title,
                    'description'  => null,
                    'content'      => $item->content,
                    'image'        => $item->image,
                    'url'          => route('news.show', $item->slug),
                    'source'       => 'Internal Article',
                    'country'      => null,
                    'published_at' => $item->created_at,
                    'sentiment'    => null,
                    'created_at'   => $item->created_at,
                ];
            });

        // Gabungkan dan urutkan berdasarkan tanggal terbaru
        $news = $newsCache->merge($articles)
            ->sortByDesc('published_at')
            ->values();

        // Pagination manual
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $total = $news->count();
        $news = $news->slice(($currentPage - 1) * $perPage, $perPage)->values();

        // Buat objek pagination sederhana
        $news = new \Illuminate\Pagination\LengthAwarePaginator(
            $news,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Sentiment summary (hanya dari NewsCache)
        $sentimentCounts = NewsCache::selectRaw("sentiment, COUNT(*) as count")
            ->groupBy('sentiment')
            ->pluck('count', 'sentiment')
            ->toArray();

        $totalNews = $total;

        return view('news.index', compact('news', 'category', 'sentimentCounts', 'totalNews'));
    }

    public function show($slug)
    {
        $article = Article::where('slug', $slug)
            ->where('status', 'Published')
            ->firstOrFail();

        return view('news.show', compact('article'));
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
