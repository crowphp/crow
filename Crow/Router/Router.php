<?php

declare(strict_types=1);

namespace Crow\Router;

use Crow\Handlers\RouteDispatchHandler;
use Crow\Http\DefaultHeaders;
use Crow\Router\Types\RouteHandler;
use Crow\Router\Types\RouteMethod;
use Crow\Router\Types\RouteMiddlewareCollection;
use Crow\Router\Types\RoutePath;
use FastRoute;
use Crow\Http\ResponseBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Crow\Router\Exceptions\RoutingLogicException;
use Psr\Http\Server\MiddlewareInterface;
use React\Http\Message\Response;

class Router implements RouterInterface
{
    private string $currentGroupPrefix = "";
    private RouteMiddlewareCollection $currentGroupMiddlewares;

    /** @var Route[] */
    private array $routeMap = [];

    public function __construct(
        RouteMiddlewareCollection $middlewareCollection
    ) {

        $this->currentGroupMiddlewares = $middlewareCollection;
    }

    public function addRoute(string $httpMethod, string $route, callable $handler): RouterInterface
    {

        $this->routeMap[] = new Route(
            new RouteMethod($httpMethod),
            new RoutePath($route, $this->currentGroupPrefix),
            new RouteHandler($handler),
            $this->currentGroupMiddlewares
        );

        return $this;
    }

    public function middleware(MiddlewareInterface|callable $handler): RouterInterface
    {
        $route = $this->routeMap[array_key_last($this->routeMap)];
        $routeMiddlewares = $route->getMiddlewareCollection()->add(
            $handler
        );

        $this->routeMap[array_key_last($this->routeMap)] = new Route(
            $route->getRouteMethod(),
            $route->getRoutePath(),
            $route->getHandler(),
            $routeMiddlewares
        );

        return $this;
    }

    public function addGroup(string $prefix, callable $callback, MiddlewareInterface|callable ...$groupHandlers): void
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $previousGroupMiddlewares = clone $this->currentGroupMiddlewares;

        $this->currentGroupPrefix = $previousGroupPrefix . $prefix;

        foreach ($groupHandlers as $handler) {
            $this->currentGroupMiddlewares = $this->currentGroupMiddlewares->add($handler);
        }
        $callback($this);
        $this->currentGroupPrefix = $previousGroupPrefix;
        $this->currentGroupMiddlewares = $previousGroupMiddlewares;
    }

    public function get(string $route, callable $handler): RouterInterface
    {
        $this->addRoute(RouteMethod::GET, $route, $handler);
        return $this;
    }

    public function post(string $route, callable $handler): RouterInterface
    {
        $this->addRoute(RouteMethod::POST, $route, $handler);
        return $this;
    }

    public function put(string $route, callable $handler): RouterInterface
    {
        $this->addRoute(RouteMethod::PUT, $route, $handler);
        return $this;
    }

    public function delete(string $route, callable $handler): RouterInterface
    {
        $this->addRoute(RouteMethod::DELETE, $route, $handler);
        return $this;
    }

    public function patch(string $route, callable $handler): RouterInterface
    {
        $this->addRoute(RouteMethod::PATCH, $route, $handler);
        return $this;
    }

    public function head(string $route, callable $handler): RouterInterface
    {
        $this->addRoute(RouteMethod::HEAD, $route, $handler);
        return $this;
    }

    /**
     * @return Route[]
     */
    public function getRouteMap(): array
    {
        return $this->routeMap;
    }
}
