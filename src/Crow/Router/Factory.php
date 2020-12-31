<?php declare(strict_types=1);

namespace Crow\Router;

class Factory
{
    public static function make(): RouterInterface
    {
        return new FastRouter;
    }
}