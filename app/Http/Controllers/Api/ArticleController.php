<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(title="Innoscripta News Aggregator API", version="1.0.0")
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     description="Enter your Bearer token in the format: 'Bearer {token}'"
 * )
 */
class ArticleController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/articles",
     *     tags={"Articles"},
     *     summary="Get list of articles",
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
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'articles_' . md5(json_encode($request->query()));

        $articles = cache()->remember($cacheKey, now()->addHours(1), function () use ($request) {
            return Article::query()
                ->when($request->keyword, function ($query, $keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('content', 'like', "%{$keyword}%");
                })
                ->when($request->date, function ($query, $date) {
                    $query->whereDate('published_at', $date);
                })
                ->when($request->category, function ($query, $category) {
                    $query->whereHas('category', function ($q) use ($category) {
                        $q->where('categories.name', $category);
                    });
                })
                ->when($request->source, function ($query, $source) {
                    $query->whereHas('source', function ($q) use ($source) {
                        $q->where('sources.name', $source);
                    });
                })
                ->with(['source:id,name', 'author:id,name', 'category:id,name'])
                ->orderBy('published_at', 'desc')
                ->paginate(10);
        });

        return $this->successResponse($articles);
    }

    /**
     * @OA\Get(
     *     path="/articles/{id}",
     *     summary="Find article by ID",
     *     operationId="getArticle",
     *     tags={"Articles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         description="ID of article to return",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
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
    public function show($id)
    {
        $cacheKey = "article_{$id}";

        $article = cache()->remember($cacheKey, now()->addHours(1), function () use ($id) {
            return Article::with(['source:id,name', 'author:id,name', 'category:id,name'])
                ->findOrFail($id);
        });

        return $this->successResponse($article);
    }
}
