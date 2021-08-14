<?php

declare(strict_types=1);

namespace Crow\Middlewares;

use FastRoute;
use Crow\Router\Route;
use Crow\Http\ResponseBuilder;
use Crow\Router\RouterInterface;
use Crow\Handlers\RouteDispatchHandler;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Crow\Router\DispatcherFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Crow\Router\Exceptions\RoutingLogicException;

class RoutingMiddleware implements MiddlewareInterface
{

    private RouterInterface $router;
    private DispatcherFactoryInterface $dispatcherFactory;
    private RouteDispatchHandler $routeDispatchHandler;

    public function __construct(
        DispatcherFactoryInterface $dispatcherFactory,
        RouteDispatchHandler $routeDispatchHandler,
        RouterInterface $router
    ) {
        $this->dispatcherFactory = $dispatcherFactory;
        $this->routeDispatchHandler = $routeDispatchHandler;
        $this->router = $router;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $dispatcher = $this->dispatcherFactory->make($this->router->getRouteMap());
        $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                return ResponseBuilder::makeResponseWithCodeAndBody(
                    404,
                    "Not Found"
                );
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                return ResponseBuilder::makeResponseWithCodeAndBody(
                    405,
                    "Method not allowed"
                );
            case FastRoute\Dispatcher::FOUND:
                array_shift($routeInfo);
                list($handlers, $vars) = $routeInfo;
                return call_user_func(
                    $this->routeDispatchHandler,
                    $handlers[Route::MIDDLEWARES_LABEL],
                    $handlers[Route::HANDLER_LABEL],
                    $request,
                    $vars
                );
        }

        throw new RoutingLogicException('Something went wrong in routing.');
    }
}
