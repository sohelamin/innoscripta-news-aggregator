<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewYorkTimesFetcher implements NewsFetcherInterface
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
                $response = Http::get("{$this->baseUrl}/{$category->name}.json", [
                    'api-key' => $this->apiKey,
                ]);

                if ($response->successful()) {
                    $articles = $response->json()['results'] ?? [];

                    $processArticles = [];
                    foreach ($articles as $article) {
                        if (!empty($article['title']) && !empty($article['abstract'])) {
                            $processArticles[] = [
                                'title' => $article['title'],
                                'content' => $article['abstract'] ?? null,
                                'url' => $article['url'],
                                'image_url' => $article['multimedia'][0]['url'] ?? null,
                                'published_at' => $article['published_date'] ? Carbon::parse($article['published_date']) : null,
                                'author' => $article['byline'] ?? null,
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
