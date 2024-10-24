<?php

namespace App\Exceptions;

class BadRequestException extends CustomException
{
    const BAD_REQUEST_EXCEPTION = "BAD_REQUEST_EXCEPTION";

    public function getStatusCode(): int
    {
        return 400;
    }
}
