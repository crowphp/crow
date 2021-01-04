<?php declare(strict_types=1);

namespace Crow\Handlers;

use Exception;
use LogicException;

class ErrorHandler
{

    /**
     * @param Exception|LogicException $exception
     * @return string
     */
    public static function exceptionToBody(Exception|LogicException $exception): string
    {
        return "Uncaught Error: " . $exception->getMessage() .
            " on line " . $exception->getFile() . ":" . $exception->getLine() . PHP_EOL .
            $exception->getTraceAsString();
    }
}