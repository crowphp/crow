<?php declare(strict_types=1);

namespace Crow\Router;

use FastRoute;
use function FastRoute\simpleDispatcher as simpleDispatcher;
use Crow\Http\DefaultHeaders;
use Crow\Http\ResponseBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Crow\Router\Exceptions\RoutingLogicException;

class FastRouter implements RouterInterface
{
    protected string $currentGroupPrefix = "";
    public const HTTP_METHOD_LABEL = "HTTP_METHOD";
    public const ROUTE_LABEL = "ROUTE";
    public const HANDLER_LABEL = "HANDLER";
    private array $routeMap = [];

    public function __construct(private DispatcherFactoryInterface $dispatcherFactory)
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
                    "Not Found");
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                return ResponseBuilder::makeResponseWithCodeAndBody(
                    405,
                    "Method not allowed");
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                return $handler(
                    $request,
                    new Response(200,
                        array_merge(
                            DefaultHeaders::get(),
                            [
                                "Host" => $request->getHeader("host")
                            ]
                        )
                    ),
                    ...array_values($vars));
        }

        throw new RoutingLogicException('Something went wrong in routing.');
    }

}