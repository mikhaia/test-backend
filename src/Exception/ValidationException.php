<?php

namespace App\Exception;

class ValidationException extends \Exception
{
    private $errors;

    public function __construct(array $errors, $message = "Validation error", $code = 400, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
