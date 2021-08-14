<?php

declare(strict_types=1);

namespace Crow\Router;

use Crow\Router\Types\RouteHandler;
use Crow\Router\Types\RouteMethod;
use Crow\Router\Types\RouteMiddlewareCollection;
use Crow\Router\Types\RoutePath;

class Route
{
    private RouteMethod $routeMethod;
    private RoutePath $routePath;
    private RouteHandler $handler;
    private RouteMiddlewareCollection $middlewareCollection;

    public const HANDLER_LABEL = "HANDLERS";
    public const MIDDLEWARES_LABEL = "MIDDLEWARES";

    public function __construct(
        RouteMethod $routeMethod,
        RoutePath $routePath,
        RouteHandler $handler,
        RouteMiddlewareCollection $middlewareCollection
    ) {
        $this->handler = $handler;
        $this->routeMethod = $routeMethod;
        $this->middlewareCollection = $middlewareCollection;
        $this->routePath = $routePath;
    }


    public function getRouteMethod(): RouteMethod
    {
        return $this->routeMethod;
    }

    public function getRoutePath(): RoutePath
    {
        return $this->routePath;
    }

    /**
     * @return array<string, array|(callable)>
     */
    public function getHandlers(): array
    {
        return [
            self::HANDLER_LABEL => $this->handler->getCallable(),
            self::MIDDLEWARES_LABEL => $this->middlewareCollection->toArray()
        ];
    }

    public function getHandler(): RouteHandler
    {
        return $this->handler;
    }

    public function getMiddlewareCollection(): RouteMiddlewareCollection
    {
        return $this->middlewareCollection;
    }
}
