<?php

declare(strict_types=1);

namespace Crow\Router\Types;

use Psr\Http\Server\MiddlewareInterface;

class RouteMiddlewareCollection
{

    /** @var array<MiddlewareInterface|callable> */
    private array $middlewares;

    /**
     * @param array<callable|MiddlewareInterface> $middlewares
     */
    public function __construct(array $middlewares = [])
    {
        $this->middlewares = $middlewares;
    }

    public function add(MiddlewareInterface|callable $middleware): self
    {
        $middlewares = $this->middlewares;
        $middlewares[] = $middleware;
        return new self($middlewares);
    }


    /**
     * @return callable[]|MiddlewareInterface[]
     */
    public function toArray(): array
    {
        return $this->middlewares;
    }
}
