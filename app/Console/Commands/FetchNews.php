<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Services\NewsFetcherManager;
use Exception;
use Illuminate\Console\Command;

class FetchNews extends Command
{
    protected $signature = 'news:fetch';
    protected $description = 'Fetch news from multiple sources';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(NewsFetcherManager $manager)
    {
        $fetchers = $manager->fetchers();

        foreach ($fetchers as $fetcher) {
            $articles = $fetcher->fetchNews();

            try {
                $this->storeNews($articles);
            } catch (Exception $e) {
                continue;
            }
        }
    }

    private function storeNews(array $news)
    {
        Article::insert($news);
    }
}
