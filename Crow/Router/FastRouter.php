<?php

declare(strict_types=1);

namespace Crow\Router;

use Crow\Handlers\RouteDispatchHandler;
use Crow\Http\DefaultHeaders;
use FastRoute;
use Crow\Http\ResponseBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Crow\Router\Exceptions\RoutingLogicException;
use Psr\Http\Server\MiddlewareInterface;
use React\Http\Message\Response;

class FastRouter implements RouterInterface
{
    private DispatcherFactoryInterface $dispatcherFactory;
    private RouteDispatchHandler $routeDispatchHandler;
    private string $currentGroupPrefix = "";
    private string $currentRouteMethod = "";
    private string $currentRoutePath = "";
    /**
     * @var array<callable>
     */
    private array $currentGroupMiddlewares = [];
    public const HTTP_METHOD_LABEL = "HTTP_METHOD";
    public const ROUTE_LABEL = "ROUTE";
    public const HANDLER_LABEL = "HANDLERS";
    public const MIDDLEWARES_LABEL = "MIDDLEWARES";
    /**
     * @var array<array>
     */
    private array $routeMap = [];

    public function __construct(
        DispatcherFactoryInterface $dispatcherFactory,
        RouteDispatchHandler       $routeDispatchHandler
    )
    {
        $this->dispatcherFactory = $dispatcherFactory;
        $this->routeDispatchHandler = $routeDispatchHandler;
    }

    /**
     * Adds a route to the collection.
     *
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string $httpMethod
     * @param string $route
     * @param callable $handler
     * @return RouterInterface
     */
    public function addRoute(string $httpMethod, string $route, callable $handler): RouterInterface
    {
        $route = $this->currentGroupPrefix . $route;
        $this->currentRoutePath = $route;
        $this->currentRouteMethod = $httpMethod;
        array_push($this->routeMap, [
            self::HTTP_METHOD_LABEL => $httpMethod,
            self::ROUTE_LABEL => $route,
            self::HANDLER_LABEL => [
                self::HANDLER_LABEL => $handler,
                self::MIDDLEWARES_LABEL => $this->currentGroupMiddlewares
            ]
        ]);

        return $this;
    }

    public function middleware(MiddlewareInterface|callable $handler): RouterInterface
    {

        array_push(
            $this->routeMap[array_key_last($this->routeMap)][self::HANDLER_LABEL][self::MIDDLEWARES_LABEL],
            $handler
        );

        return $this;
    }

    /**
     * Create a route group with a common prefix.
     *
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string $prefix
     * @param callable $callback
     * @param mixed ...$groupHandlers
     */
    public function addGroup(string $prefix, callable $callback, mixed ...$groupHandlers): void
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $previousGroupMiddlewares = $this->currentGroupMiddlewares;
        $this->currentGroupPrefix = $previousGroupPrefix . $prefix;
        if (count($groupHandlers) > 0) {
            $this->currentGroupMiddlewares = $groupHandlers;
        }
        $callback($this);
        $this->currentGroupPrefix = $previousGroupPrefix;
        $this->currentGroupMiddlewares = $previousGroupMiddlewares;
    }

    /**
     * Adds a GET route to the collection
     *
     * This is simply an alias of $this->addRoute('GET', $route, $handler)
     *
     * @param string $route
     * @param callable $handler
     */
    public function get(string $route, callable $handler): RouterInterface
    {
        $this->addRoute('GET', $route, $handler);
        return $this;
    }

    /**
     * Adds a POST route to the collection
     *
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $route
     * @param callable $handler
     * @return RouterInterface
     */
    public function post(string $route, callable $handler): RouterInterface
    {
        $this->addRoute('POST', $route, $handler);
        return $this;
    }

    /**
     * Adds a PUT route to the collection
     *
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $route
     * @param callable $handler
     * @return RouterInterface
     */
    public function put(string $route, callable $handler): RouterInterface
    {
        $this->addRoute('PUT', $route, $handler);
        return $this;
    }

    /**
     * Adds a DELETE route to the collection
     *
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $route
     * @param callable $handler
     * @return RouterInterface
     */
    public function delete(string $route, callable $handler): RouterInterface
    {
        $this->addRoute('DELETE', $route, $handler);
        return $this;
    }

    /**
     * Adds a PATCH route to the collection
     *
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler)
     *
     * @param string $route
     * @param callable $handler
     * @return RouterInterface
     */
    public function patch(string $route, callable $handler): RouterInterface
    {
        $this->addRoute('PATCH', $route, $handler);
        return $this;
    }

    /**
     * Adds a HEAD route to the collection
     *
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     *
     * @param string $route
     * @param callable $handler
     * @return RouterInterface
     */
    public function head(string $route, callable $handler): RouterInterface
    {
        $this->addRoute('HEAD', $route, $handler);
        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $dispatcher = $this->dispatcherFactory->make($this->routeMap);
        $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                return ResponseBuilder::makeResponseWithCodeAndBody(
                    404,
                    "Not Found"
                );
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                return ResponseBuilder::makeResponseWithCodeAndBody(
                    405,
                    "Method not allowed"
                );
            case FastRoute\Dispatcher::FOUND:
                array_shift($routeInfo);
                list($handlers, $vars) = $routeInfo;
                return call_user_func(
                    $this->routeDispatchHandler,
                    $handlers[self::MIDDLEWARES_LABEL],
                    $handlers[self::HANDLER_LABEL],
                    $request,
                    $vars
                );
        }

        throw new RoutingLogicException('Something went wrong in routing.');
    }
}
