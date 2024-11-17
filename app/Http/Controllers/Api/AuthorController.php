<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthorController extends ApiController
{
    /**
     * Fetch authors.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Retrieve Single Author.
     *
     * @param int $id
     * @return JsonResponse
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
