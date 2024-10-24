<?php

namespace App\Console\Commands;

use App\Jobs\SendNotificationJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-notifications-command';

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
        Log::info('SendNotificationJob is starting.');
        SendNotificationJob::dispatch();
        Log::info('SendNotificationJob has dispatched the job.');
    }
}
