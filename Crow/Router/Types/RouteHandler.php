<?php

declare(strict_types=1);

namespace Crow\Router\Types;

class RouteHandler
{
    /** callable */
    private $handler;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function getCallable(): callable
    {
        return $this->handler;
    }
}
