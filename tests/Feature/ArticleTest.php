<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Article listing test.
     */
    public function test_article_listing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get('/api/articles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'content',
                            'url',
                            'image_url',
                            'published_at',
                            'source',
                            'author',
                            'category',
                        ],
                    ],
                ],
            ]);
    }

    /**
     * Article details test.
     */
    public function test_article_details(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get('/api/articles/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'title',
                    'content',
                    'url',
                    'image_url',
                    'published_at',
                    'source',
                    'author',
                    'category',
                ],
            ]);
    }
}
