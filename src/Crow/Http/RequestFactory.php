<?php
declare(strict_types=1);

namespace Crow\Http;


use Psr\Http\Message\RequestInterface;
use Swoole\Http\Request as SwooleRawReq;
use React\Http\Message\ServerRequest as ReactResponse;
use Nyholm\Psr7\Factory\Psr17Factory;

class RequestFactory
{

    public static function create(SwooleRawReq|ReactResponse $request): RequestInterface
    {
        switch ($request::class) {
            case SwooleRawReq::class:
                return new SwooleRequest(
                    $request,
                    new Psr17Factory,
                    new Psr17Factory
                );
            case ReactResponse::class:
                return $request;
        }
    }
}