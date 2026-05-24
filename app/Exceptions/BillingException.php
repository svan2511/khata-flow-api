<?php

namespace App\Exceptions;

use Exception;

class BillingException extends Exception
{
    public function __construct(string $message = 'Billing error', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
