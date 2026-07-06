<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsCache;
use App\Services\NewsService;

class SyncNews extends Command
{
    protected $signature = 'news:sync';

    protected $description = 'Sync news from GNews API';

    public function handle(NewsService $service)
    {
        $news = $service->getNews();

        if (!$news || !isset($news['articles'])) {

            $this->error("Failed to retrieve news.");

            return;
        }

        foreach ($news['articles'] as $article) {

            try {

                NewsCache::updateOrCreate(

                    [
                        'url' => $article['url']
                    ],

                    [

                        'country_id' => null,

                        'title' => $article['title'] ?? null,

                        'description' => $article['description'] ?? null,

                        'content' => $article['content'] ?? null,

                        'url' => $article['url'],

                        'image' => $article['image'] ?? null,

                        'source' => $article['source']['name'] ?? null,

                        'published_at' => $article['publishedAt'] ?? now()

                    ]

                );

                $this->info("Synced : ".$article['title']);

           } catch (\Exception $e) {

    $this->error("Failed : ".$article['title']);
    $this->error($e->getMessage());

}

        }

        $this->info("==============================");

        $this->info("News Sync Completed!");

        $this->info("==============================");
    }
}