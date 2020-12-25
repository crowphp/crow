<?php
declare(strict_types=1);

namespace Crow\Router;

use Crow\ResponseBuilder;
use FastRoute;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Crow\ErrorHandler;
use Crow\DefaultHeaders;
use Crow\Router\Exceptions\RoutingLogicException;

class FastRouter implements RouterInterface
{
    protected string $currentGroupPrefix = "";
    private const HTTP_METHOD_LABEL = "HTTP_METHOD";
    private const ROUTE_LABEL = "ROUTE";
    private const HANDLER_LABEL = "HANDLER";


    public function __construct(private array $routeMap = [])
    {
    }

    /**
     * Adds a route to the collection.
     *
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string|string[] $httpMethod
     * @param string $route
     * @param mixed $handler
     */
    public function addRoute(array|string $httpMethod, string $route, mixed $handler)
    {
        $route = $this->currentGroupPrefix . $route;
        foreach ((array)$httpMethod as $method) {
            array_push($this->routeMap, [
                self::HTTP_METHOD_LABEL => $method,
                self::ROUTE_LABEL => $route,
                self::HANDLER_LABEL => $handler
            ]);
        }
    }


    /**
     * Create a route group with a common prefix.
     *
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string $prefix
     * @param callable $callback
     */
    public function addGroup(string $prefix, callable $callback)
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $this->currentGroupPrefix = $previousGroupPrefix . $prefix;
        $callback($this);
        $this->currentGroupPrefix = $previousGroupPrefix;
    }

    /**
     * Adds a GET route to the collection
     *
     * This is simply an alias of $this->addRoute('GET', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function get(string $route, mixed $handler)
    {
        $this->addRoute('GET', $route, $handler);
    }

    /**
     * Adds a POST route to the collection
     *
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function post(string $route, mixed $handler)
    {
        $this->addRoute('POST', $route, $handler);
    }

    /**
     * Adds a PUT route to the collection
     *
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function put(string $route, mixed $handler)
    {
        $this->addRoute('PUT', $route, $handler);
    }

    /**
     * Adds a DELETE route to the collection
     *
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function delete(string $route, mixed $handler)
    {
        $this->addRoute('DELETE', $route, $handler);
    }

    /**
     * Adds a PATCH route to the collection
     *
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function patch(string $route, mixed $handler)
    {
        $this->addRoute('PATCH', $route, $handler);
    }

    /**
     * Adds a HEAD route to the collection
     *
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function head(string $route, mixed $handler)
    {
        $this->addRoute('HEAD', $route, $handler);
    }

    /**
     * @return FastRoute\Dispatcher
     */
    private function makeDispatcher(): FastRoute\Dispatcher
    {
        return FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
            foreach ($this->routeMap as $route) {
                $r->addRoute(
                    $route[self::HTTP_METHOD_LABEL],
                    $route[self::ROUTE_LABEL],
                    $route[self::HANDLER_LABEL]);
            }
        });
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $dispatcher = $this->makeDispatcher();
        $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                return ResponseBuilder::makeResponseWithCodeAndBody(
                    404,
                    "Not Found");
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                return ResponseBuilder::makeResponseWithCodeAndBody(
                    405,
                    "Method not allowed");
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                try {
                    return $handler(
                        $request,
                        new Response(200, DefaultHeaders::get()),
                        ...array_values($vars));
                } catch (\Exception | \LogicException $exception) {
                    return ResponseBuilder::makeResponseWithCodeAndBody(
                        500,
                        ErrorHandler::exceptionToBody($exception));
                }
        }

        throw new RoutingLogicException('Something went wrong in routing.');
    }

}