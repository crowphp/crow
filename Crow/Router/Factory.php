<?php

declare(strict_types=1);

namespace Crow\Router;

use Crow\Handlers\QueueRequestHandler;
use Crow\Handlers\RouteDispatchHandler;

class Factory
{
    /**
     * @return RouterInterface
     */
    public static function make(): RouterInterface
    {
        return new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );
    }
}
