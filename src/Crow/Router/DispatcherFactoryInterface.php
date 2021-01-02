<?php declare(strict_types=1);

namespace Crow\Router;

interface DispatcherFactoryInterface
{

    /**
     * @param array $routeMap
     * @return mixed
     */
    public function make(array $routeMap);
}