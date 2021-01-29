<?php declare(strict_types=1);

namespace Crow\Handlers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class QueueRequestHandler implements RequestHandlerInterface
{

    private array $middleware = [];

    public function add(MiddlewareInterface|callable $middleware)
    {
        $this->middleware[] = $middleware;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = array_shift($this->middleware);

        if (is_callable($middleware)) {
            return $middleware($request, $this);
        }
        return $middleware->process($request, $this);
    }
}