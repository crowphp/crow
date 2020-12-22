<?php

namespace Crow\Router;
class Factory
{
    public static function make(): RouterInterface
    {
        return new FastRouter;
    }
}