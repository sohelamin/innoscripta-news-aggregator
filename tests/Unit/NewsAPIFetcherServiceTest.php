<?php

namespace Tests\Unit;

use App\Services\NewsAPIFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewsAPIFetcherServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Fetch news test.
     */
    public function test_fetch_news_data()
    {
        Http::fake([
            env('NEWSAPI_URL') . '/top-headlines*' => Http::response([
                'articles' => [
                    [
                        'title' => 'Test Article 1',
                        'description' => 'Test Content 1',
                        'url' => 'https://myurl.com/article-1',
                        'publishedAt' => '2024-11-22 12:00:00',
                        'author' => 'John',
                    ],
                    [
                        'title' => 'Test Article 2',
                        'description' => 'Test Content 2',
                        'url' => 'https://myurl.com/article-2',
                        'publishedAt' => '2024-11-22 12:00:00',
                        'author' => 'Doe',
                    ],
                ],
            ], 200),
        ]);

        $service = new NewsAPIFetcher(env('NEWSAPI_URL'), env('NEWSAPI_KEY'));
        $articles = $service->fetchNews([
            (object) [
                'id' => 1,
                'name' => 'technology',
            ]
        ]);

        $this->assertCount(2, $articles);
        $this->assertEquals('Test Article 1', $articles[0]['title']);
    }

    /**
     * Fetch news fail test.
     */
    public function test_fetch_news_fail()
    {
        Http::fake([
            env('NEWSAPI_URL') . '/top-headlines*' => Http::response([], 500),
        ]);

        $service = new NewsAPIFetcher(env('NEWSAPI_URL'), env('NEWSAPI_KEY'));
        $articles = $service->fetchNews([
            (object) [
                'id' => 1,
                'name' => 'technology',
            ]
        ]);

        $this->assertEmpty($articles);
    }
}
