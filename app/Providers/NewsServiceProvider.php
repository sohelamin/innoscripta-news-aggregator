<?php

namespace App\Providers;

use App\Services\NewsAPIFetcher;
use App\Services\NewsFetcherManager;
use App\Services\NewYorkTimesFetcher;
use App\Services\TheGuardianFetcher;
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

            $manager->registerFetcher(
                'TheGuardian',
                new TheGuardianFetcher(
                    env('THE_GUARDIAN_API_URL'),
                    env('THE_GUARDIAN_API_KEY')
                )
            );

            $manager->registerFetcher(
                'NewYorkTimes',
                new NewYorkTimesFetcher(
                    env('NEW_YORK_TIMES_API_URL'),
                    env('NEW_YORK_TIMES_API_KEY')
                )
            );

            return $manager;
        });
    }
}
