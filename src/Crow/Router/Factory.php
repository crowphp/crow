<?php declare(strict_types=1);

namespace Crow\Router;

class Factory
{
    /**
     * @return RouterInterface
     */
    public static function make(): RouterInterface
    {
        return new FastRouter(new FastRouteDispatcher());
    }
}