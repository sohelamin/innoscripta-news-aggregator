<?php

namespace App\Services;

interface NewsFetcherInterface
{
    public function fetchNews(): array;
}
