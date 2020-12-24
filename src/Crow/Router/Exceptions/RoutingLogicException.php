<?php

namespace Crow\Router\Exceptions;

use LogicException;
use Throwable;

class RoutingLogicException extends LogicException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}