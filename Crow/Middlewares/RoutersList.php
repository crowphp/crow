<?php

namespace Crow\Middlewares;

use Crow\Router\RouterInterface;

class RoutersList
{

    /**
     * @var RouterInterface[]
     */
    private array $routers = [];

    public function add(RouterInterface $router): void
    {
        $this->routers[] = $router;
    }

    /**
     * @return RouterInterface[]
     */
    public function getRouters(): array
    {
        return $this->routers;
    }
}