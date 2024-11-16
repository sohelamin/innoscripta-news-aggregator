<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\SourceController;
use App\Http\Controllers\Api\UserPreferenceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:30,1'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
});

Route::middleware(['throttle:60,1', 'auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware(['ability:logout']);
    Route::get('/user', [AuthController::class, 'user'])
        ->middleware(['ability:user-info']);

    Route::post('/user/preferences', [UserPreferenceController::class, 'setPreferences'])
        ->middleware(['ability:set-preferences']);
    Route::get('/user/preferences', [UserPreferenceController::class, 'getPreferences'])
        ->middleware(['ability:get-preferences']);
    Route::get('/user/news-feed', [UserPreferenceController::class, 'personalizedFeed'])
        ->middleware(['ability:personalized-feed']);

    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{id}', [ArticleController::class, 'show']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    Route::get('/authors', [AuthorController::class, 'index']);
    Route::get('/authors/{id}', [AuthorController::class, 'show']);

    Route::get('/sources', [SourceController::class, 'index']);
    Route::get('/sources/{id}', [SourceController::class, 'show']);
});
