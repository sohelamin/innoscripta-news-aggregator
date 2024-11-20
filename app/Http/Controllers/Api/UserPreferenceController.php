<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPreferenceController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/api/user/preferences",
     *     tags={"Preferences"},
     *     summary="Set the preferences",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"source_ids", "category_ids", "author_ids"},
     *                 @OA\Property(
     *                     property="source_ids",
     *                     type="array",
     *                     description="Source IDs",
     *                     @OA\Items(
     *                         type="integer"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="category_ids",
     *                     type="array",
     *                     description="Category IDs",
     *                     @OA\Items(
     *                         type="integer"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="author_ids",
     *                     type="array",
     *                     description="Author IDs",
     *                     @OA\Items(
     *                         type="integer"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/user/preferences",
     *     tags={"Preferences"},
     *     summary="Get the preferences",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->successResponse(['items' => $user->preferences]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/news-feed",
     *     tags={"Preferences"},
     *     summary="Get the preferences",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function personalizedFeed(Request $request): JsonResponse
    {
        $user = $request->user();

        $articles = Article::byUserPreferences($user)->get();
        return $this->successResponse(['items' => $articles]);
    }
}
