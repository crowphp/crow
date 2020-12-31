<?php declare(strict_types=1);

namespace Crow\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class QueueRequestHandler implements RequestHandlerInterface
{

    private array $middleware = [];

    public function __construct()
    {
    }

    public function add(MiddlewareInterface|callable $middleware)
    {
        $this->middleware[] = $middleware;
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        $middleware = array_shift($this->middleware);

        if (is_callable($middleware)) {
            return $middleware($request, $this);
        }
        return $middleware->process($request, $this);
    }
}