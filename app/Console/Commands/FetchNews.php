<?php

namespace App\Console\Commands;

use App\Jobs\FetchNewsJob;
use App\Services\NewsFetcherManager;
use Illuminate\Console\Command;

class FetchNews extends Command
{
    /**
     * Command signature.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * Description for the command.
     *
     * @var string
     */
    protected $description = 'Fetch news from multiple sources';

    /**
     * Constructor function.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handle the command execution.
     *
     * @param NewsFetcherManager $manager
     * @return void
     */
    public function handle(NewsFetcherManager $manager)
    {
        $fetchers = $manager->fetchers();

        foreach ($fetchers as $source => $fetcher) {
            FetchNewsJob::dispatch($source, $fetcher);
            $this->info("Dispatched job for source: {$source}");
        }
    }
}
