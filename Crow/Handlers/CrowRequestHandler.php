<?php declare(strict_types=1);

namespace Crow\Handlers;

use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\RouterInterface;

abstract class CrowRequestHandler
{
    protected RouterInterface $router;
    protected UserMiddlewaresList $middlewaresList;
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function setMiddlewaresList(UserMiddlewaresList $middlewaresList)
    {
        $this->middlewaresList = $middlewaresList;
    }
}