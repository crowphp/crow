<?php declare(strict_types=1);

namespace Crow\Http\Server;

use Crow\Http\Server\Exceptions\InvalidEventType;
use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\RouterInterface;
use Psr\Http\Server\MiddlewareInterface;

abstract class BaseServer implements ServerInterface
{

    protected RouterInterface $router;
    protected UserMiddlewaresList $middlewaresList;
    public mixed $server;
    protected array $eventListeners = [];
    protected int $loopTimeoutSeconds = 0;
    protected array $invalidEvents = ['request'];

    function __construct( UserMiddlewaresList $middlewaresList)
    {
        $this->middlewaresList = $middlewaresList;
    }

    protected function attachListeners()
    {
        foreach ($this->eventListeners as $eventListener) {
            $this->server->on($eventListener["eventName"], $eventListener["callback"]);
        }
    }

    /**
     * @param string $event
     * @param callable $callback
     */
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

    /**
     * Use method provides an interface for users to add middlewares
     * @param MiddlewareInterface|callable $middleware
     */
    public function use(MiddlewareInterface|callable $middleware)
    {
        $this->middlewaresList->add($middleware);
    }

}