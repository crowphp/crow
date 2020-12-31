<?php declare(strict_types=1);

namespace Crow\Router;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutingMiddleware implements MiddlewareInterface
{

    public function __construct(private RouterInterface $router)
    {
    }

    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->router->dispatch($request);
    }
}