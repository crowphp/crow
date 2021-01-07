<?php declare(strict_types=1);

namespace Crow\Middlewares;

use Psr\Http\Server\MiddlewareInterface;

class UserMiddlewaresList
{

    private array $middlewares = [];

    public function add(MiddlewareInterface|callable $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}