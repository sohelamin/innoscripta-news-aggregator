<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Category listing test.
     */
    public function test_category_listing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                        ],
                    ],
                ],
            ]);
    }

    /**
     * Category details test.
     */
    public function test_category_details(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get('/api/categories/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                ],
            ]);
    }
}
