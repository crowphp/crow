<?php declare(strict_types=1);

namespace Crow\Http\Server;

use Crow\Http\QueueRequestHandler;
use Crow\Http\Server\Exceptions\InvalidEventType;
use Crow\Router\RouterInterface;
use Crow\Router\RoutingMiddleware;
use Psr\Http\Server\MiddlewareInterface;

abstract class BaseServer implements ServerInterface
{

    protected RouterInterface $router;
    protected mixed $server;
    protected array $eventListeners = [];
    protected int $loopTimeoutSeconds = 0;
    protected array $invalidEvents = ['request'];
    protected array $middleware = [];

    protected function attachListeners()
    {
        foreach ($this->eventListeners as $eventListener) {
            $this->server->on($eventListener["eventName"], $eventListener["callback"]);
        }
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
        array_push($this->eventListeners, [
            "eventName" => $event,
            "callback" => $callback
        ]);
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