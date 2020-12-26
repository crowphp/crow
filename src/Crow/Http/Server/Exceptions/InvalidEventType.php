<?php


namespace Crow\Http\Server\Exceptions;


use Throwable;

class InvalidEventType extends \LogicException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}