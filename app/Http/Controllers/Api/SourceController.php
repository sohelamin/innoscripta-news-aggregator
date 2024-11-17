<?php

namespace App\Http\Controllers\Api;

use App\Models\Source;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SourceController extends ApiController
{
    /**
     * Fetch sources.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Retrieve Single Source.
     *
     * @param int $id
     * @return JsonResponse
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
