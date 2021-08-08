<?php

declare(strict_types=1);

namespace Crow\Http;

use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\ServerRequestFactory;

class Request extends ServerRequest implements ServerRequestInterface
{

    /**
     * @phpstan-ignore-next-line
     */
    public static function makeRequest(string $method, string $uri, array $serverParams = []): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest($method, $uri, $serverParams);
    }
}
