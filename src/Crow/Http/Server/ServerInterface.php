<?php declare(strict_types=1);

namespace Crow\Http\Server;

use Crow\Router\RouterInterface;


interface ServerInterface
{
    /**
     * Encapsulating function that initialize and starts all the required
     * services before listening on the given port for HTTP calls.
     * @param int $port
     * @param string $host
     */

    public function listen(int $port = 5000, string $host="127.0.0.1");

    /**
     * Function to set timeout after which the server loop stops
     * Useful when writing unit tests.
     * @param int $seconds
     */
    public function withTimeout(int $seconds);


    /**
     * Hook function to pass listeners for events to LoopInterface
     * @param string $event
     * @param callable $callback
     */
    public function on(string $event, callable $callback);


    /**
     * Sets the RouterInterface implementation with the server
     * for routes and middleware handling.
     * @param RouterInterface $router
     */
    public function withRouter(RouterInterface $router);

    /**
     * Adds new middlewares to the global handlers for the server
     * @param callable $middleware
     */
    public function use(callable $middleware);
}