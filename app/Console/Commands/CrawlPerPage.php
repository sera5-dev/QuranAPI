<?php

namespace App\Console\Commands;

use App\Lib\QuranHelper;
use App\Models\User;
use App\Support\DripEmailer;
use Illuminate\Console\Command;

class CrawlPerPage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:per_page';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl data from external server (per page)';

    /**
     * Execute the console command.
     *
     * @param  \App\Support\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        $this->info("\nCrawling total page(s)...");
        $count = QuranHelper::getAllQuranPagesCount();
        $this->info("\nTotal page(s): $count\n");

        foreach (range(1, $count) as $page) {
            $this->info("\nParsing page [$page]...");
            QuranHelper::fetchPage($page);

            $secSleep = rand(3, 4);

            $this->info("Parsing page [$page] completed. Crawler will sleep for $secSleep seconds...");
            sleep($secSleep);
        }
    }
}
