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
        $articles = Article::query()
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                      ->orWhere('content', 'like', "%{$keyword}%");
            })
            ->when($request->date, function ($query, $date) {
                $query->whereDate('published_at', $date);
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('id', $categoryId);
                });
            })
            ->when($request->source_id, function ($query, $sourceId) {
                $query->where('source_id', $sourceId);
            })
            ->with(['source:id,name', 'author:id,name', 'categories:id,name'])
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return $this->successResponse($articles);
    }

    /**
     * Retrieve Single Article.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show($id)
    {
        $article = Article::with(['source', 'author', 'categories'])->findOrFail($id);

        return $this->successResponse($article);
    }
}
