<?php


namespace Crow\Http\Server;


use Crow\Http\QueueRequestHandler;
use Crow\Http\Server\Exceptions\InvalidEventType;
use Crow\Router\RouterInterface;
use Crow\Router\RoutingMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

abstract class BaseServer implements ServerInterface
{


    protected RouterInterface $router;
    protected mixed $server;
    protected array $eventListeners = [];
    protected int $loopTimeoutSeconds = 0;
    protected array $invalidEvents = ['request'];
    protected array $middleware = [];


    public function __construct(protected QueueRequestHandler $requestHandler)
    {
    }

    protected function attachListeners()
    {
        foreach ($this->eventListeners as $event => $handler) {
            $this->server->on($event, $handler);
        }
    }

    protected function handle(RequestInterface $request): ResponseInterface
    {
        return $this->requestHandler->handle($request);
    }

    protected function makeMiddlewareHandlerForRequest(): QueueRequestHandler
    {
        $requestHandler = new QueueRequestHandler();
        foreach ($this->middleware as $middleware) {
            $requestHandler->add($middleware);
        }
        $requestHandler->add(new RoutingMiddleware($this->router));
        return $requestHandler;
    }


    public function on(string $event, callable $callback)
    {
        if (in_array($event, $this->invalidEvents)) {
            throw new InvalidEventType("This event type is not permitted");
        }
        array_push($this->eventListeners, $event);
    }

    /**
     * Sets the RouterInterface implementation with the server
     * for routes and middleware handling.
     * @param RouterInterface $router
     */
    public function withRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    /**
     * Function to set timeout after which the server loop stops
     * Useful when writing unit tests.
     * @param int $seconds
     */
    public function withTimeout(int $seconds)
    {
        $this->loopTimeoutSeconds = $seconds;
    }

    public function use(MiddlewareInterface|callable $middleware)
    {
        $this->middleware[] = $middleware;
    }

}