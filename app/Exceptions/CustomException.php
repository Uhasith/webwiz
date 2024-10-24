<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{   
    public function render($request)
    {       
        return response()->json(["staus" => $this->getCode(), "message" => $this->getMessage()],$this->getCode());       
    }

}
