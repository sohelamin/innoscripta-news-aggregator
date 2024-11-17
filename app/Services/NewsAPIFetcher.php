<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

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

        $category = $categories[0];

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

            return $processArticles;
        }

        return [];
    }
}
