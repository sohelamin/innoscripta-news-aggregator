<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Registration test.
     */
    public function test_registration(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/register', [
            'name' => 'New Test User',
            'email' => 'new_test_user@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'name',
                    'token',
                ],
            ]);
    }

    /**
     * Login test.
     */
    public function test_login(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/login', [
            'email' => 'test@example.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'token',
                ],
            ]);
    }

    /**
     * Invalid Login test.
     */
    public function test_invalid_login(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/login', [
            'email' => 'test1@example.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(400);
    }

    /**
     * Logout test.
     */
    public function test_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
        ->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/logout');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ]);
    }

    /**
     * User test.
     */
    public function test_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
        ->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ]);
    }

    /**
     * Unauthorized test.
     */
    public function test_unauthorized(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/user');

        $response->assertStatus(401);
    }
}
