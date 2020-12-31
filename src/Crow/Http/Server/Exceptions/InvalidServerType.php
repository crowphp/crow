<?php declare(strict_types=1);

namespace Crow\Http\Server\Exceptions;

use \LogicException;
use \Throwable;

class InvalidServerType extends LogicException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}