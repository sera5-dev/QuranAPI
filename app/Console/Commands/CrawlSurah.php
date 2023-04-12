<?php

namespace App\Console\Commands;

use App\Lib\QuranCrawler;
use Illuminate\Console\Command;

class CrawlSurah extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:surah';

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
        array_map('unlink', array_filter((array) glob(storage_path("app/data/surah/*"))));
        array_map('unlink', array_filter((array) glob(storage_path("app/data/juz/*"))));
        array_map('unlink', array_filter((array) glob(storage_path("app/data/pages/*"))));


        $this->info("Reading surah from remote source...");

        foreach (range(1, 114) as $surah) {
            $this->info("Reading QS. [$surah] from remote source...");
            QuranCrawler::newCrawlSurah($surah);

            $sleepTime = rand(2, 4);
            $this->info("Crawling QS. [$surah] done! Sleeping for $sleepTime second(s)...");
            sleep($sleepTime);
        }

        $this->info("Crawling surah completed. Now generating Big Quran file...");
        $this->call("quran:big-generate");
    }
}
