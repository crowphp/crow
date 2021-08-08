<?php

declare(strict_types=1);

namespace Crow\Http;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request as SwooleRawReq;

class RequestFactory
{

    public function create(SwooleRawReq|ServerRequestInterface $request): ServerRequestInterface
    {
        return match ($request::class) {
            SwooleRawReq::class => new SwooleRequest(
                $request,
                new Psr17Factory(),
                new Psr17Factory()
            ),
            default => $request,
        };
    }
}
