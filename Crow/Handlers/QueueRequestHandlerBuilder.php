<?php


namespace Crow\Handlers;


use Crow\Middlewares\ErrorMiddleware;
use Crow\Middlewares\RoutingMiddleware;
use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\RouterInterface;

class QueueRequestHandlerBuilder
{

    function build(UserMiddlewaresList $middlewaresList, RouterInterface $router): QueueRequestHandler
    {
        $queueRequestHandler = new QueueRequestHandler();
        $queueRequestHandler->add(new ErrorMiddleware());
        foreach ($middlewaresList->getMiddlewares() as $middleware) {
            $queueRequestHandler->add($middleware);
        }
        $queueRequestHandler->add(new RoutingMiddleware($router));
        return $queueRequestHandler;
    }
}