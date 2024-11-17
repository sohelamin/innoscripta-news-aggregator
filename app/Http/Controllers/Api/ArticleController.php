<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends ApiController
{
    /**
     * Fetch articles with filtering and pagination.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Retrieve Single Article.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $cacheKey = "article_{$id}";

        $article = cache()->remember($cacheKey, now()->addHours(1), function () use ($id) {
            return Article::with(['source:id,name', 'author:id,name', 'categories:id,name'])
                ->findOrFail($id);
        });

        return $this->successResponse($article);
    }
}
