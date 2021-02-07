<?php

declare(strict_types=1);

namespace Crow\Handlers;

use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\RouterInterface;

/**
 * Class CrowRequestHandler
 * @Ca
 * @package Crow\Handlers
 */
abstract class CrowRequestHandler
{
    /**
     * @var RouterInterface
     */
    protected RouterInterface $router;
    /**
     * @var UserMiddlewaresList
     */
    protected UserMiddlewaresList $middlewaresList;

    /**
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    /**
     * @param UserMiddlewaresList $middlewaresList
     */
    public function setMiddlewaresList(UserMiddlewaresList $middlewaresList): void
    {
        $this->middlewaresList = $middlewaresList;
    }
}
