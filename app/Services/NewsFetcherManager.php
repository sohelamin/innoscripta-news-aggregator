<?php

namespace App\Services;

class NewsFetcherManager
{
    /**
     * List of fetchers.
     *
     * @var array
     */
    protected array $fetchers = [];

    /**
     * Register new fetcher.
     *
     * @param string $key
     * @param NewsFetcherInterface $fetcher
     * @return void
     */
    public function registerFetcher(string $key, NewsFetcherInterface $fetcher): void
    {
        $this->fetchers[$key] = $fetcher;
    }

    /**
     * Fetch news from all sources together.
     *
     * @return array
     */
    public function fetchFromAllSources(): array
    {
        $news = [];

        foreach ($this->fetchers as $key => $fetcher) {
            $news[$key] = $fetcher->fetchNews();
        }

        return $news;
    }

    /**
     * Get the list of fetchers.
     *
     * @return array
     */
    public function fetchers(): array
    {
        return $this->fetchers;
    }
}
