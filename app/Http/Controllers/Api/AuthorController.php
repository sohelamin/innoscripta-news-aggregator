<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthorController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/authors",
     *     tags={"Authors"},
     *     summary="Get list of authors",
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
        $page = $request->has('page') ? $request->query('page') : 1;
        $cacheKey = "authors_page_$page";

        $authors = cache()->remember($cacheKey, now()->addHours(1), function () {
            return Author::orderBy('id', 'desc')
                ->paginate(20);
        });

        return $this->successResponse($authors);
    }

    /**
     * @OA\Get(
     *     path="/authors/{id}",
     *     summary="Find author by ID",
     *     operationId="getAuthor",
     *     tags={"Authors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         description="ID of author to return",
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
        $cacheKey = "author_{$id}";

        $author = cache()->remember($cacheKey, now()->addHours(1), function () use ($id) {
            return Author::findOrFail($id);
        });

        return $this->successResponse($author);
    }
}
