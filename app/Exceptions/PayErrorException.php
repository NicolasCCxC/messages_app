<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PayErrorException extends Exception
{
    public function __construct(Throwable $previous = null)
    {
        $message = 'DECLINED';
        parent::__construct($message, Response::HTTP_BAD_REQUEST, $previous);
    }
}
