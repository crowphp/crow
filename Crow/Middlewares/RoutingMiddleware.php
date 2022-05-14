<?php

declare(strict_types=1);

namespace Crow\Middlewares;

use Crow\Handlers\RouteDispatchHandler;
use Crow\Http\ResponseBuilder;
use Crow\Router\DispatcherFactoryInterface;
use Crow\Router\Exceptions\RoutingLogicException;
use Crow\Router\Route;
use FastRoute;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutingMiddleware implements MiddlewareInterface
{

    private RoutersList $routerList;
    private DispatcherFactoryInterface $dispatcherFactory;
    private RouteDispatchHandler $routeDispatchHandler;

    public function __construct(
        DispatcherFactoryInterface $dispatcherFactory,
        RouteDispatchHandler $routeDispatchHandler,
        RoutersList $routersList
    ) {
        $this->dispatcherFactory = $dispatcherFactory;
        $this->routeDispatchHandler = $routeDispatchHandler;
        $this->routerList = $routersList;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeInfo = $this->findRouteInfo($request);

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

    private function findRouteInfo(ServerRequestInterface $request): ?array
    {
        $routeInfo = null;
        foreach ($this->routerList->getRouters() as $router) {
            $routeInfo = $this->dispatcherFactory->make($router->getRouteMap())->dispatch(
                $request->getMethod(),
                $request->getUri()->getPath()
            );

            if ($routeInfo[0] === FastRoute\Dispatcher::FOUND) {
                return $routeInfo;
            }
        }
        return $routeInfo;
    }
}
