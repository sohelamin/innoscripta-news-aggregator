<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class NewsAPIFetcher implements NewsFetcherInterface
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct(string $baseUrl, string $apiKey)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
    }

    public function fetchNews(): array
    {
        $category = 'technology';

        $response = Http::get("{$this->baseUrl}/top-headlines", [
            'apiKey' => $this->apiKey,
            'country' => 'us',
            'category' => $category,
        ]);

        if ($response->successful()) {
            $articles = $response->json()['articles'] ?? [];

            return array_map(function ($item) use ($category) {
                return [
                    'title' => $item['title'],
                    'content' => $item['description'] ?? null,
                    'url' => $item['url'],
                    'image_url' => $item['urlToImage'] ?? null,
                    'published_at' => $item['publishedAt'] ? Carbon::parse($item['publishedAt']) : null,
                    // 'author' => $item['author'] ?? null,
                    // 'source' => $item['source']['name'] ?? null,
                    // 'category' => $category,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $articles);
        }

        return [];
    }
}
