<?php

namespace App\Providers;

use App\Services\NewsAPIFetcher;
use App\Services\NewsFetcherManager;
use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(NewsFetcherManager::class, function ($app) {
            $manager = new NewsFetcherManager();

            $manager->registerFetcher(
                'NewsAPI',
                new NewsAPIFetcher(
                    env('NEWSAPI_URL'),
                    env('NEWSAPI_KEY')
                )
            );

            return $manager;
        });
    }
}
