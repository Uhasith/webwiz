<?php

namespace App\Helpers;

class Messages{

    public static string $registrationSubject = "Welcome to Air Quality Monitoring Dashboard";
    public static string $forgotPasswordSubject = "Reset Your Password";
    public static string $airQualityUpdateSubject = "Air Quality Index Update";

    public static string $userNotFound = "The email is invalid";
    public static string $otpIncorrect = "OTP is incorrect. Please check the user email and try again, or contact support if the issue persists.";
    public static string $passwordNotMatch = "The password you entered is incorrect. Please try again or reset your password if you've forgotten it.";
 }
