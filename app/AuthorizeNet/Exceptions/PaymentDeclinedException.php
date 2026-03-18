<?php

namespace App\AuthorizeNet\Exceptions;

class PaymentDeclinedException extends AuthorizeNetException
{
    public function __construct(string $message = 'Payment was declined', int $code = 2, array $errors = [])
    {
        parent::__construct($message, $code, null, $errors);
    }
}
