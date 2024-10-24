<?php

namespace App\Jobs;

use App\Helpers\Utility;
use App\Repository\User\FirebaseRepo;
use App\Repository\User\NotificationsRepo;
use App\Service\User\FirebaseService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $notificationRepo = new NotificationsRepo();
        $firebaseRepo = new FirebaseRepo();
        $firbaseService = new FirebaseService($firebaseRepo);

        $notifications = $notificationRepo->getAllScheduled(Carbon::now(Utility::$sriLankaTimeZone));
        $users = $firebaseRepo->getAll();

        foreach ($notifications as $notification) {
            $data = [
                'receiver_id' => $users->toArray(),
                'title' => $notification['title'],
                'description' => $notification['description'],
            ];

            $firbaseService->sendNotificationToMultiple($data);
            $notification->status = Utility::$statusInactive;
            $notificationRepo->save($notification);
            $notification->save();
        }

    }
}
