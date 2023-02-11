<?php

namespace App\Console\Commands;

use App\Lib\QuranHelper;
use App\Models\User;
use App\Support\DripEmailer;
use Illuminate\Console\Command;

class Combine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:combine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Combine crawled data';

    /**
     * Execute the console command.
     *
     * @param  \App\Support\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        QuranHelper::combine();
    }
}
