<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheGuardianFetcher implements NewsFetcherInterface
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
                $response = Http::get("{$this->baseUrl}/search", [
                    'api-key' => $this->apiKey,
                    'section' => $category->name,
                    'page-size' => 20,
                    'show-fields' => 'headline,body',
                    'show-tags' => 'contributor',
                ]);

                if ($response->successful()) {
                    $articles = $response->json()['response']['results'] ?? [];

                    $processArticles = [];
                    foreach ($articles as $article) {
                        if (!empty($article['webTitle'])) {
                            $authors = [];
                            if (isset($item['tags'])) {
                                foreach ($item['tags'] as $tag) {
                                    if ($tag['type'] === 'contributor') {
                                        $authors[] = $tag['webTitle'];
                                    }
                                }
                            }

                            $processArticles[] = [
                                'title' => $article['webTitle'],
                                'content' => $item['fields']['body'] ?? null,
                                'url' => $article['webUrl'],
                                'image_url' => null,
                                'published_at' => $article['webPublicationDate'] ? Carbon::parse($article['webPublicationDate']) : null,
                                'author' => $authors[0] ?? null,
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
