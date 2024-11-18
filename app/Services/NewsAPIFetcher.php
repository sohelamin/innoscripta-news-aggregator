<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsAPIFetcher implements NewsFetcherInterface
{
    /**
     * Base URL for this fetcher.
     *
     * @var string
     */
    protected string $baseUrl;

    /**
     * API key for this fetcher.
     *
     * @var string
     */
    protected string $apiKey;

    /**
     * Constructor function.
     *
     * @param string $baseUrl
     * @param string $apiKey
     */
    public function __construct(string $baseUrl, string $apiKey)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
    }

    /**
     * Fetch the news.
     *
     * @param array $categories
     * @return array
     */
    public function fetchNews($categories = []): array
    {
        if (empty($categories)) {
            return [];
        }

        $processAllArticles = [];
        foreach ($categories as $category) {
            try {
                $response = Http::get("{$this->baseUrl}/top-headlines", [
                    'apiKey' => $this->apiKey,
                    'country' => 'us',
                    'category' => $category->name,
                ]);

                if ($response->successful()) {
                    $articles = $response->json()['articles'] ?? [];

                    $processArticles = [];
                    foreach ($articles as $article) {
                        if (!empty($article['title']) && !empty($article['description'])) {
                            $processArticles[] = [
                                'title' => $article['title'],
                                'content' => $article['description'],
                                'url' => $article['url'],
                                'image_url' => $article['urlToImage'] ?? null,
                                'published_at' => $article['publishedAt'] ? Carbon::parse($article['publishedAt']) : null,
                                'author' => $article['author'] ?? null,
                                'category_id' => $category->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    $processAllArticles = array_merge($processAllArticles, $processArticles);
                }
            } catch (Exception $e) {
                Log::error(get_class($this) . ' Fetching Error: ' . $e->getMessage());
            }
        }

        return $processAllArticles;
    }
}
