<?php

namespace Test\Unit\Crow\Router;

use Crow\Router\Types\RouteHandler;
use Crow\Router\Types\RouteMethod;
use Crow\Router\Types\RouteMiddlewareCollection;
use Crow\Router\Types\RoutePath;
use FastRoute;
use Crow\Router\FastRouteDispatcher;
use Crow\Router\Route;
use PHPUnit\Framework\TestCase;

class FastRouteDispatcherTest extends TestCase
{

    public function testMake()
    {
        $fastRouteDispatcher = new FastRouteDispatcher();
        $dispatcher = $fastRouteDispatcher->make([
            new Route(
                RouteMethod::GET(),
                new RoutePath(""),
                new RouteHandler(function(){}),
                new RouteMiddlewareCollection()
            )
        ]);

        $this->assertTrue($dispatcher instanceof FastRoute\Dispatcher);
    }
}
