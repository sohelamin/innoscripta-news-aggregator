<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Forgot password test.
     */
    public function test_forgot_password(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ]);
    }

    /**
     * Reset password test.
     */
    public function test_reset_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Password Reset Test User',
            'email' => 'password_reset_test@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $token = Password::createToken($user);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/reset-password', [
            'token' => $token,
            'email' => 'password_reset_test@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ]);

        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }
}
