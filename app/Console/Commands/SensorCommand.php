<?php

namespace App\Console\Commands;

use App\Jobs\SensorsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SensorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:five-minute-command';

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
        Log::info('SensorCommand is starting.');
        SensorsJob::dispatch();
        Log::info('SensorCommand has dispatched the job.');
    }
}
