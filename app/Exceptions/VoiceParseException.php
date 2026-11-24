<?php

namespace App\Exceptions;

use Exception;

class VoiceParseException extends Exception
{
    public function __construct(string $message = 'Voice parsing error', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
