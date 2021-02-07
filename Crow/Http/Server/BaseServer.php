<?php

declare(strict_types=1);

namespace Crow\Http\Server;

use Crow\Http\Server\Exceptions\InvalidEventType;
use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\RouterInterface;
use Psr\Http\Server\MiddlewareInterface;

abstract class BaseServer implements ServerInterface
{

    protected RouterInterface $router;
    protected UserMiddlewaresList $middlewaresList;
    /**
     * @var array[]
     */
    protected array $eventListeners = [];
    protected int $loopTimeoutSeconds = 0;
    /**
     * @var string[]
     */
    protected array $invalidEvents = ['request'];
    /**
     * @var array[]
     */
    protected array $configs = [];
    protected mixed $server;

    public function __construct(UserMiddlewaresList $middlewaresList)
    {
        $this->middlewaresList = $middlewaresList;
    }

    protected function attachListeners(): void
    {
        foreach ($this->eventListeners as $eventListener) {
            $this->server->on($eventListener["eventName"], $eventListener["callback"]);
        }
    }

    /**
     * @param string $event
     * @param callable $callback
     */
    public function on(string $event, callable $callback): void
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
    public function withTimeout(int $seconds): void
    {
        $this->loopTimeoutSeconds = $seconds;
    }

    /**
     * Use method provides an interface for users to add middlewares
     * @param MiddlewareInterface|callable $middleware
     */
    public function use(MiddlewareInterface | callable $middleware): void
    {
        $this->middlewaresList->add($middleware);
    }


    /**
     * @param array[] $configs
     */
    public function configs(array $configs): void
    {
        $this->configs = $configs;
    }
}
