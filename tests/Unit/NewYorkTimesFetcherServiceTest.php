<?php

namespace Tests\Unit;

use App\Services\NewYorkTimesFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewYorkTimesFetcherServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Fetch news test.
     */
    public function test_fetch_news_data()
    {
        Http::fake([
            env('NEW_YORK_TIMES_API_URL') . '/technology.json*' => Http::response([
                'results' => [
                    [
                        'title' => 'Test Article 1',
                        'abstract' => 'Test Content 1',
                        'url' => 'https://myurl.com/article-1',
                        'multimedia' => [
                            [
                                'url' => 'https://myurl.com/article-1.jpg',
                            ],
                        ],
                        'published_date' => '2024-11-22 12:00:00',
                        'byline' => 'John',
                    ],
                    [
                        'title' => 'Test Article 2',
                        'abstract' => 'Test Content 2',
                        'url' => 'https://myurl.com/article-2',
                        'multimedia' => [
                            [
                                'url' => 'https://myurl.com/article-2.jpg',
                            ],
                        ],
                        'published_date' => '2024-11-22 12:00:00',
                        'byline' => 'Doe',
                    ],
                ],
            ], 200),
        ]);

        $service = new NewYorkTimesFetcher(env('NEW_YORK_TIMES_API_URL'), env('NEW_YORK_TIMES_API_KEY'));
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
            env('NEW_YORK_TIMES_API_URL') . '/technology.json*' => Http::response([], 500),
        ]);

        $service = new NewYorkTimesFetcher(env('NEW_YORK_TIMES_API_URL'), env('NEW_YORK_TIMES_API_KEY'));
        $articles = $service->fetchNews([
            (object) [
                'id' => 1,
                'name' => 'technology',
            ]
        ]);

        $this->assertEmpty($articles);
    }
}
