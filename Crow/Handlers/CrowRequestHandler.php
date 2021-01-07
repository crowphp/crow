<?php declare(strict_types=1);

namespace Crow\Handlers;

use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\RouterInterface;

interface CrowRequestHandler
{
    public function setRouter(RouterInterface $router);

    public function setMiddlewaresList(UserMiddlewaresList $middlewaresList);
}