<?php

namespace App\Http\Controllers\Api;

use App\Models\Source;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SourceController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/sources",
     *     tags={"Sources"},
     *     summary="Get list of sources",
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
        $cacheKey = "sources_page_$page";

        $sources = cache()->remember($cacheKey, now()->addHours(12), function () {
            return Source::orderBy('id', 'desc')
                ->paginate(20);
        });

        return $this->successResponse($sources);
    }

    /**
     * @OA\Get(
     *     path="/sources/{id}",
     *     summary="Find source by ID",
     *     operationId="getSource",
     *     tags={"Sources"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         description="ID of source to return",
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
        $cacheKey = "source_{$id}";

        $source = cache()->remember($cacheKey, now()->addHours(12), function () use ($id) {
            return Source::findOrFail($id);
        });

        return $this->successResponse($source);
    }
}
