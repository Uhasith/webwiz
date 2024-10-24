<?php

namespace App\Console\Commands;

use App\Jobs\HourlyJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HourlyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:hourly-command';

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
        Log::info('HourlyJob is starting.');
        HourlyJob::dispatch();
        Log::info('HourlyJob has dispatched the job.');
    }
}
