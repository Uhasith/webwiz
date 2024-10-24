<?php

namespace App\Console\Commands;

use App\Jobs\MonthlyJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonthlyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monthly-command';

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
        Log::info('monthlyJob is starting.');
        MonthlyJob::dispatch();
        Log::info('monthlyJob has dispatched the job.');
    }
}
