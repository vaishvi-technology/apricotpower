<?php

namespace App\AuthorizeNet\Exceptions;

use Exception;

class AuthorizeNetException extends Exception
{
    protected array $errors = [];

    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null, array $errors = [])
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
