<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class CrawlEgavetProducts extends Command
{
    protected $signature = 'egavet:links';

    protected $description = 'Collect product links from Egavet';

    public function handle(): int
    {
        $html = Http::get('https://www.egavet.com/products_main')->body();

        $crawler = new Crawler($html);

        $links = $crawler
            ->filter('a')
            ->each(fn ($node) => $node->attr('href'));

        $links = collect($links)
            ->filter()
            ->unique()
            ->values();

        foreach ($links as $link) {
            $this->line($link);
        }

        $this->info('Total: '.$links->count());

        return self::SUCCESS;
    }
}
