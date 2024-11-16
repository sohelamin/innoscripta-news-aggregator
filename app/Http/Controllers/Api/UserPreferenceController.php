<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPreferenceController extends ApiController
{
    /**
     * Set user preferences.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setPreferences(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'source_ids' => 'array',
            'source_ids.*' => 'integer|exists:sources,id',
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:categories,id',
            'author_ids' => 'array',
            'author_ids.*' => 'integer|exists:authors,id',
        ]);

        $user->preferences()->delete();

        foreach ($validated['source_ids'] ?? [] as $sourceId) {
            $user->preferences()->create(['source_id' => $sourceId]);
        }
        foreach ($validated['category_ids'] ?? [] as $categoryId) {
            $user->preferences()->create(['category_id' => $categoryId]);
        }
        foreach ($validated['author_ids'] ?? [] as $authorId) {
            $user->preferences()->create(['author_id' => $authorId]);
        }

        return $this->successResponse([], 'Preferences updated successfully.');
    }

    /**
     * Get the preferences.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->successResponse(['items' => $user->preferences]);
    }

    /**
     * Get the personalized news.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function personalizedFeed(Request $request): JsonResponse
    {
        $user = $request->user();

        $articles = Article::byUserPreferences($user)->get();
        return $this->successResponse(['items' => $articles]);
    }
}
