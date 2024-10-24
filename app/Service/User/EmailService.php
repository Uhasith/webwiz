<?php

namespace App\Service\User;

use App\Helpers\Messages;
use App\Mail\EmailSending;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService{

    public static function registerEmail($data): string
    {

        $data['url'] = env('APP_URL') . '/login';
        $subject = Messages::$registrationSubject;
        return self::sendEmail($subject, $data, 'emails.registeration');

    }

    public static function forgetEmail($data): string
    {
        $subject = Messages::$forgotPasswordSubject;
        return self::sendEmail($subject, $data, 'emails.forget-password');

    }

    public static function airQualityUpdateEmail($data): string
    {
        $data['url'] = '#';
        $subject = Messages::$airQualityUpdateSubject;
        return self::sendEmail($subject, $data, 'emails.air-quality-update');

    }

    public static function sendEmail($subject,$data,$template): string
    {

        Mail::to($data['email'])->send(new EmailSending($subject, $data, $template));
        return "Email sent successfully!";
    }
}
