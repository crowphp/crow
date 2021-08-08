<?php

declare(strict_types=1);

namespace Crow\Http;

use Psr\Http\Message\ResponseInterface;

class ResponseBuilder
{
    public static function makeResponseWithCodeAndBody(int $code, string $body): ResponseInterface
    {
        $response = new Response(
            "php://memory",
            $code,
            array_merge(
                DefaultHeaders::get(),
                [
                    "Content-Type" => "text/plain"
                ]
            )
        );
        $response->getBody()->write($body);

        return $response;
    }
}
