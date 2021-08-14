<?php

declare(strict_types=1);

namespace Crow\Router;

use Crow\Handlers\QueueRequestHandler;
use Crow\Handlers\RouteDispatchHandler;
use Crow\Router\Types\RouteMiddlewareCollection;

class Factory
{
    /**
     * @return RouterInterface
     */
    public static function make(): RouterInterface
    {
        return new Router(
            new RouteMiddlewareCollection()
        );
    }
}
