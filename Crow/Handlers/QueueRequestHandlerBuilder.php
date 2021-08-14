<?php

declare(strict_types=1);

namespace Crow\Handlers;

use Crow\Middlewares\ErrorMiddleware;
use Crow\Middlewares\RoutingMiddleware;
use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\FastRouteDispatcher;
use Crow\Router\RouterInterface;

class QueueRequestHandlerBuilder
{

    public function build(UserMiddlewaresList $middlewaresList, RouterInterface $router): QueueRequestHandler
    {
        $queueRequestHandler = new QueueRequestHandler();
        $queueRequestHandler->add(new ErrorMiddleware());
        foreach ($middlewaresList->getMiddlewares() as $middleware) {
            $queueRequestHandler->add($middleware);
        }
        $queueRequestHandler->add(
            new RoutingMiddleware(
                new FastRouteDispatcher(),
                new RouteDispatchHandler(new QueueRequestHandler()),
                $router
            )
        );
        return $queueRequestHandler;
    }
}
