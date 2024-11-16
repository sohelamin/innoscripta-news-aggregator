<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    /**
     * Name of the token.
     *
     * @var string
     */
    private string $tokenName = 'my-app';

    /**
     * User abilities.
     *
     * @var array
     */
    private array $abilities = [
        'logout',
        'user-info',
        'set-preferences',
        'get-preferences',
        'personalized-feed',
    ];

    /**
     * Register new user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:64',
            'password_confirmation' => 'required|string|min:8|max:64|same:password',
        ]);

        $payload = $request->all();
        $payload['password'] = Hash::make($payload['password']);

        $user = User::create($payload);

        $token = $user->createToken($this->tokenName, $this->abilities)
            ->plainTextToken;

        return $this->successResponse([
            'name' => $user->name,
            'token' => $token,
        ], 'You have been successfully registered.');
    }

    /**
     * Login and generate new token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:64',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('The provided credentials are incorrect.', [
                'email' => ['Please check email address.'],
                'password' => ['Please check password.'],
            ]);
        }

        $token = $user->createToken($this->tokenName, $this->abilities)
            ->plainTextToken;

        return $this->successResponse([
            'token' => $token,
        ], 'You have been successfully logged in.');
    }

    /**
     * Logout and revoke token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse([], 'You have been successfully logged out.');
    }

    /**
     * Get the current logged in user's info.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
