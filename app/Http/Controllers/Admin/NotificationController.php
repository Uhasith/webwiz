<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Service\User\NotificationsService;
use Illuminate\Http\Request;

class NotificationController extends Controller{

    private $notificationService;
    
    public function __construct( NotificationsService $notificationService)
    {
        $this->notificationService =  $notificationService;
       
    }

    public function scheduleNotification(Request $request)
    {
        $data = [
            'sender_id' =>  $request->input('sender_id') ,
            'receiver_id' =>  $request->input('receiver_id'),
            'type' =>  $request->input('type'),
            'title' =>  $request->input('title') ,
            'description' =>  $request->input('description'),
            'time' =>  $request->input('time'),  
        ];

        return $this->notificationService->scheduleNotification($data);
    }


}