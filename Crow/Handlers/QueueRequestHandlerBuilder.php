<?php

declare(strict_types=1);

namespace Crow\Handlers;

use Crow\Middlewares\ErrorMiddleware;
use Crow\Middlewares\RoutersList;
use Crow\Middlewares\RoutingMiddleware;
use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\FastRouteDispatcher;

class QueueRequestHandlerBuilder
{

    public function build(UserMiddlewaresList $middlewaresList, RoutersList $routersList): QueueRequestHandler
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
                $routersList
            )
        );


        return $queueRequestHandler;
    }
}
