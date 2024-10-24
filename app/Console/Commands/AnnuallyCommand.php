<?php

namespace App\Console\Commands;

use App\Jobs\AnnuallyJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AnnuallyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:annually-command';

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
        Log::info('annuallyJob is starting.');
        AnnuallyJob::dispatch();
        Log::info('annuallyJob has dispatched the job.');
    }
}
