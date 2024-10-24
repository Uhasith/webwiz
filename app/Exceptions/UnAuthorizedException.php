<?php

namespace App\Exceptions;

class UnAuthorizedException extends CustomException
{
    const UNAUTHORIZED_REQUEST = "UNAUTHORIZED_REQUEST";
    const UNAUTHORIZED_ACTION = "UNAUTHORIZED_ACTION";
    const DEVICE_ID_NOT_FOUND = "DEVICE_ID_NOT_FOUND";

    public function getStatusCode(): int
    {
        return 401;
    }
}
