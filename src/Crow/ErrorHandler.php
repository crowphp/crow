<?php

namespace Crow;

use Exception;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;

class ErrorHandler
{

    public static function makeResponseWithCodeAndBody(int $code, string $body): ResponseInterface
    {
        return new Response(
            $code,
            array_merge(
                DefaultHeaders::get(),
                [
                    "Content-Type" => "text/plain"
                ]
            ),
            $body
        );
    }

    public static function exceptionToBody(Exception|LogicException $exception): string
    {
        return "Uncaught Error: " . $exception->getMessage() .
            " on line" . $exception->getFile() . ":" . $exception->getLine() . PHP_EOL .
            $exception->getTraceAsString();
    }
}