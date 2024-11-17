<?php

namespace App\Services;

class NewsFetcherManager
{
    protected array $fetchers = [];

    public function registerFetcher(string $key, NewsFetcherInterface $fetcher): void
    {
        $this->fetchers[$key] = $fetcher;
    }

    public function fetchFromAllSources(): array
    {
        $news = [];

        foreach ($this->fetchers as $key => $fetcher) {
            $news[$key] = $fetcher->fetchNews();
        }

        return $news;
    }

    public function fetchers(): array
    {
        return $this->fetchers;
    }
}
