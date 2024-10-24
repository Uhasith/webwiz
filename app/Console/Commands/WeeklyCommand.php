<?php

namespace App\Console\Commands;

use App\Jobs\WeeklyJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WeeklyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:weekly-command';

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
        Log::info('WeeklyJob is starting.');
        WeeklyJob::dispatch();
        Log::info('WeeklyJob has dispatched the job.');
    }
}
