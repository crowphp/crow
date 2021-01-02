<?php declare(strict_types=1);

namespace Crow\Router;

use FastRoute;
use function FastRoute\simpleDispatcher;

class FastRouteDispatcher implements DispatcherFactoryInterface
{

    /**
     * @param array $routeMap
     * @return FastRoute\Dispatcher
     */
    public function make(array $routeMap): FastRoute\Dispatcher
    {
        return simpleDispatcher(function (FastRoute\RouteCollector $r) use ($routeMap) {
            foreach ($routeMap as $route) {
                $r->addRoute(
                    $route[FastRouter::HTTP_METHOD_LABEL],
                    $route[FastRouter::ROUTE_LABEL],
                    $route[FastRouter::HANDLER_LABEL]);
            }
        });
    }
}