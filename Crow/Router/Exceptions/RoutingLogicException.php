<?php

declare(strict_types=1);

namespace Crow\Router\Exceptions;

use LogicException;
use Throwable;

class RoutingLogicException extends LogicException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        //construct is definng a method parent of these variables
        parent::__construct($message, $code, $previous);
    }
}
