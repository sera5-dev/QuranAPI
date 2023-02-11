<?php

namespace App\Console\Commands;

use App\Lib\QuranHelper;
use App\Models\User;
use App\Support\DripEmailer;
use Illuminate\Console\Command;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:crawl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl data from external server';

    /**
     * Execute the console command.
     *
     * @param  \App\Support\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        $this->info("\nCrawling surahs data...");
        QuranHelper::fetchSurahsFromQuranCom();

        $this->info("\nParsing surahs data...");
        QuranHelper::parseMeta();

        foreach (range(1, 114) as $surah) {
            $this->info("\nParsing surah [$surah]...");
            QuranHelper::crawlSurah($surah);

            $secSleep = rand(6, 8);

            $this->info("Parsing surah [$surah] completed. Crawler will sleep for $secSleep seconds...");
            sleep($secSleep);
        }
    }
}
