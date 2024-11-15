<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Name of the token.
     *
     * @var string
     */
    private string $tokenName = 'my-app';

    /**
     * Register new user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ]);

        $payload = $request->all();
        $payload['password'] = Hash::make($payload['password']);

        $user = User::create($payload);

        $token = $user->createToken($this->tokenName)->plainTextToken;

        return response()->json([
            'data' => [
                'name' => $user->name,
                'token' => $token,
            ],
            'message' => 'You have been successfully registered.',
        ], 200);
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
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($this->tokenName)->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
            ],
            'message' => 'You have been successfully logged in.',
        ], 200);
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

        return response()->json([
            'message' => 'You have been successfully logged out.',
        ], 200);
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

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }
}
