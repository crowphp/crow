<?php

declare(strict_types=1);

namespace Crow\Router;

use FastRoute;

interface DispatcherFactoryInterface
{

    /**
     * @param array[] $routeMap
     * @return FastRoute\Dispatcher
     */
    public function make(array $routeMap): FastRoute\Dispatcher;
}
