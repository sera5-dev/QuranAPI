<?php

namespace App\Console\Commands;

use App\Lib\QuranCrawler;
use Illuminate\Console\Command;

class CrawlTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:meta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info("Crawling metadata from remote sources...");
        QuranCrawler::newParseMetas();
        $this->info("done!");
    }
}
