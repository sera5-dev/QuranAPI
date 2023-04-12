<?php

namespace App\Console\Commands;

use App\Lib\BigQuranGenerator;
use Illuminate\Console\Command;

class QuranBigGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quran:big-generate';

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
        $this->info("Combining Quran...");
        BigQuranGenerator::combine();
        $this->info("done!");
    }
}
