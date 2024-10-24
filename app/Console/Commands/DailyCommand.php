<?php

namespace App\Console\Commands;

use App\Jobs\DailyJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DailyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('dailyJob is starting.');
        DailyJob::dispatch();
        Log::info('dailyJob has dispatched the job.');
    }
}
