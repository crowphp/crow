<?php declare(strict_types=1);

namespace Crow\Http;

use Laminas\Diactoros\ServerRequestFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request as SwooleRawReq;
use React\Http\Message\ServerRequest as ReactRequest;

class RequestFactory
{

    public static function create(SwooleRawReq|ReactRequest $request): ServerRequestInterface
    {
        switch ($request::class) {
            case SwooleRawReq::class:
                return new SwooleRequest(
                    $request,
                    new Psr17Factory(),
                    new Psr17Factory()
                );
            case ReactRequest::class:
                return $request;
        }
    }
}