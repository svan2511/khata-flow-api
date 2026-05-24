<?php

namespace App\Exceptions;

use Exception;

class OtpException extends Exception
{
    public function __construct(string $message = 'OTP error', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
