<?php
declare(strict_types=1);
namespace Crow;

use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;

class ResponseBuilder{
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
}