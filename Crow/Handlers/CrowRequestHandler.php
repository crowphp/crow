<?php

declare(strict_types=1);

namespace Crow\Handlers;

use Crow\Middlewares\UserMiddlewaresList;
use Crow\Middlewares\RoutersList;

abstract class CrowRequestHandler
{

    protected RoutersList $routers;

    protected UserMiddlewaresList $middlewaresList;

    public function setRouter(RoutersList $routers): void
    {
        $this->routers = $routers;
    }

    public function setMiddlewaresList(UserMiddlewaresList $middlewaresList): void
    {
        $this->middlewaresList = $middlewaresList;
    }
}
