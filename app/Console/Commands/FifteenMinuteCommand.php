<?php

namespace App\Console\Commands;

use App\Jobs\FifteenMinuteJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FifteenMinuteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fifteen-minute-command';

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
        Log::info('FifteenMinuteJob is starting.');
        FifteenMinuteJob::dispatch();
        Log::info('FifteenMinuteJob has dispatched the job.');
    }
}
