<?php

namespace App\Repository\User;

use App\ModelsV2\Firebase;

class FirebaseRepo
{

    public function save($token)
    {

        $existingToken = Firebase::where('token', $token)->first();
        if (!$existingToken) {
            $firebase = new Firebase();
            $firebase->token = $token;
            $firebase->save();

            return $firebase;
        }

        return $existingToken;
    }

    public function getAll()
    {

        return Firebase::pluck('token');

    }

    public function invalidateFcm($token)
    {
        return Firebase::where('token', $token)->delete();
    }

    public function invalidateFcmTokens(array $tokens)
    {
        return Firebase::whereIn('token', $tokens)->delete();
    }


}
