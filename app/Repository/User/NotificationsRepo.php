<?php

namespace App\Repository\User;

use App\Helpers\Utility;
use App\ModelsV2\Notifications;
use App\ModelsV2\ScheduleNotification;

class NotificationsRepo
{

    public function save($data): Notifications
    {

        $notification = new Notifications();
        $notification->user_id = $data['user_id'];
        $notification->receiver_id = json_encode($data['receiver_id']);
        $notification->type = $data['type'];
        $notification->title = $data['title'];
        $notification->description = $data['description'];
        $notification->status = Utility::$statusActive;
        $notification->save();

        return $notification;

    }


    public function getAll()
    {

        return Notifications::where('status', Utility::$statusActive)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }


    public function saveScheduledNotifications($data): ScheduleNotification
    {

        $notification = new ScheduleNotification();
        $notification->sender_id = $data['sender_id'];
        $notification->receiver_id = json_encode($data['receiver_id']);
        $notification->type = $data['type'];
        $notification->title = $data['title'];
        $notification->description = $data['description'];
        $notification->scheduled_at = $data['time'];
        $notification->status = Utility::$statusActive;

        $notification->save();

        return $notification;

    }

    public function getAllScheduled($time)
    {

        return ScheduleNotification::where('status', Utility::$statusActive)
            ->where('scheduled_at', '<=', $time)
            ->get();
    }


}
