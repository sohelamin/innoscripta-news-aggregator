<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    /**
     * Fetch categories.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->has('page') ? $request->query('page') : 1;
        $cacheKey = "categories_page_$page";

        $categories = cache()->remember($cacheKey, now()->addHours(12), function () {
            return Category::orderBy('id', 'desc')
                ->paginate(20);
        });

        return $this->successResponse($categories);
    }

    /**
     * Retrieve Single Category.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $cacheKey = "category_{$id}";

        $category = cache()->remember($cacheKey, now()->addHours(12), function () use ($id) {
            return Category::findOrFail($id);
        });

        return $this->successResponse($category);
    }
}
