<?php

namespace Tests\Unit;

use App\Services\TheGuardianFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TheGuardianFetcherServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Fetch news test.
     */
    public function test_fetch_news_data()
    {
        Http::fake([
            env('THE_GUARDIAN_API_URL') . '/search*' => Http::response([
                'response' => [
                    'results' => [
                        [
                            'webTitle' => 'Test Article 1',
                            'fields' => ['body' => 'Test Content 1'],
                            'webUrl' => 'https://myurl.com/article-1',
                            'webPublicationDate' => '2024-11-22 12:00:00',
                            'tags' => [
                                [
                                    'type' => 'contributor',
                                    'webTitle' => 'John',
                                ],
                            ],
                        ],
                        [
                            'webTitle' => 'Test Article 2',
                            'fields' => ['body' => 'Test Content 2'],
                            'webUrl' => 'https://myurl.com/article-2',
                            'webPublicationDate' => '2024-11-22 12:00:00',
                            'tags' => [
                                [
                                    'type' => 'contributor',
                                    'webTitle' => 'Doe',
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new TheGuardianFetcher(env('THE_GUARDIAN_API_URL'), env('THE_GUARDIAN_API_KEY'));
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
            env('THE_GUARDIAN_API_KEY') . '/search*' => Http::response([], 500),
        ]);

        $service = new TheGuardianFetcher(env('THE_GUARDIAN_API_KEY'), env('THE_GUARDIAN_API_KEY'));
        $articles = $service->fetchNews([
            (object) [
                'id' => 1,
                'name' => 'technology',
            ]
        ]);

        $this->assertEmpty($articles);
    }
}
