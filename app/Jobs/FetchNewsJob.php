<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Services\NewsFetcherInterface;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchNewsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Name of source.
     *
     * @var string
     */
    protected string $source;

    /**
     * Fetcher instance.
     *
     * @var NewsFetcherInterface
     */
    protected NewsFetcherInterface $fetcher;

    /**
     * Create new job instance.
     *
     * @param string $source
     * @param NewsFetcherInterface $fetcher
     */
    public function __construct(string $source, NewsFetcherInterface $fetcher)
    {
        $this->source = $source;
        $this->fetcher = $fetcher;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $categories = Category::all();
        $source = Source::where('name', $this->source)->first();

        $articles = $this->fetcher->fetchNews($categories);
        $authors = $this->processAuthors($articles);

        $articles = array_map(function ($item) use ($authors, $source) {
            $author = $item['author'];
            unset($item['author']);

            $item['author_id'] = $authors[$author]->id ?? null;
            $item['source_id'] = $source->id ?? null;

            return $item;
        }, $articles);

        try {
            $this->storeArticles($articles);
        } catch (Exception $e) {
            Log::error('News Storing Error: ', $e->getMessage());
        }
    }

    /**
     * Add if the authors are not in database and return all authors.
     *
     * @param array $articles
     * @return array
     */
    protected function processAuthors(array $articles): Collection
    {
        $authorNames = collect($articles)->pluck('author')->unique();

        $existingAuthors = Author::whereIn('name', $authorNames)
            ->get()->keyBy('name');
        $newAuthors = $authorNames->diff($existingAuthors->keys());

        $newAuthorsData = $newAuthors
            ->filter(fn($name) => !is_null($name))
            ->map(
                fn($name) => [
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

        Author::insert($newAuthorsData->toArray());

        return Author::whereIn('name', $authorNames)
            ->get()->keyBy('name');
    }

    /**
     * Store the articles into database.
     *
     * @param array $articles
     * @return void
     */
    private function storeArticles(array $articles): void
    {
        Article::insert($articles);
    }
}
