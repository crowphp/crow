<?php

declare(strict_types=1);

namespace Crow\Router;

use FastRoute;
use Exception;

use function FastRoute\simpleDispatcher;

class FastRouteDispatcher implements DispatcherFactoryInterface
{


    /**
     * @param array<array> $routeMap
     * @return FastRoute\Dispatcher
     * @throws Exception
     */
    public function make(array $routeMap): FastRoute\Dispatcher
    {
        return simpleDispatcher(function (FastRoute\RouteCollector $r) use ($routeMap) {
            foreach ($routeMap as $route) {
                $r->addRoute(
                    $route[FastRouter::HTTP_METHOD_LABEL],
                    $route[FastRouter::ROUTE_LABEL],
                    $route[FastRouter::HANDLER_LABEL]
                );
            }
        });
    }
}
