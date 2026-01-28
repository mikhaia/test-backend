<?php

namespace App\Model;

class NotFoundException extends \Exception
{
    public function __construct($message = "Not found", $code = 404, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
