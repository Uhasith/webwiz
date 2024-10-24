<?php

namespace App\Service\User;

use App\ModelsV2\ScheduleNotification;
use App\Repository\User\NotificationsRepo;

class NotificationsService{

    private NotificationsRepo $notificationRepo;

    /**
     * @param NotificationsRepo $notificationRepo
     */
    public function __construct(NotificationsRepo $notificationRepo,)
    {
        $this->notificationRepo = $notificationRepo;
    }

    public function save($data): \App\ModelsV2\Notifications
    {
        return $this->notificationRepo->save($data);
    }

    public function getAll(){
        return $this->notificationRepo->getAll();
    }

    public function scheduleNotification($data): ScheduleNotification
    {
      return $this->notificationRepo->saveScheduledNotifications($data);
  }

}
