<?php

namespace App\Service;

use App\Exceptions\CustomException;
use App\Helpers\Messages;
use App\Repository\Admin\UserRepo;
use App\Service\User\EmailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ForgetPasswordService
{

    private UserRepo $userRepo;

    /**
     * @param UserRepo $userRepo
     */
    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @throws CustomException
     */
    public function sendOtp($email): void
    {
        $otp = str()->random(5);
        $user = $this->userRepo->getUserByEmail($email);

        if($user == null){
            throw new CustomException(Messages::$userNotFound,401);
        }

        // TODO:: check otp expiration here

        $user->otp = $otp;
        $user->otp_expired_at = Carbon::now()->addMinutes(10);
        $this->userRepo->save($user);

            $data = [
                'username' => $user->name,
                'email' => $user->email,
                'code' => $otp,
            ];

            EmailService::forgetEmail($data);


    }

    /**
     * @throws CustomException
     */
    public function resetPassword($confirmPassword, $password, $otp, $email): void
    {
        $user = $this->userRepo->getUserByOTP($email,$otp);
        if($user == null){
            throw new CustomException(Messages::$otpIncorrect,401);
        }

        if ($password !== $confirmPassword) {
            throw new CustomException(Messages::$passwordNotMatch,400);
        }

        $user->password = Hash::make($password);
        $user->otp_expired_at = null;
        $this->userRepo->save($user);

    }
}
