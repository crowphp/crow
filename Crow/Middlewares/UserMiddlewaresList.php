<?php

declare(strict_types=1);

namespace Crow\Middlewares;

use Psr\Http\Server\MiddlewareInterface;

class UserMiddlewaresList
{

    /**
     * @var mixed[]
     */
    private array $middlewares = [];

    public function add(MiddlewareInterface | callable $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @return mixed[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
