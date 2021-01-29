<?php declare(strict_types=1);

namespace Crow\Http;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request as SwooleRawReq;
use React\Http\Message\ServerRequest as ReactRequest;

class RequestFactory
{

    public function create(SwooleRawReq|ReactRequest|ServerRequestInterface $request): ServerRequestInterface
    {
        $serverRequest = null;
        switch ($request::class) {
            case SwooleRawReq::class:
                $serverRequest = new SwooleRequest(
                    $request,
                    new Psr17Factory(),
                    new Psr17Factory()
                );
                break;
            case ReactRequest::class:
                $serverRequest = $request;
                break;
        }
        return $serverRequest;
    }
}