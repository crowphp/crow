<?php


namespace Crow;

use React;
use Crow\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;

class Server
{

    private RouterInterface $router;
    private LoopInterface $loop;
    private array $eventListeners;
    private int $loopTimeoutSeconds = 0;

    private function loopTimeout()
    {
        if ($this->loopTimeoutSeconds > 0) {
            $loop = $this->loop;
            $this->loop->addTimer($this->loopTimeoutSeconds, function () use ($loop) {
                echo "Loop timeout enabled, stopping server" . PHP_EOL;
                $loop->stop();
            });
        }
    }

    private function makeServer(): React\Http\Server
    {
        $router = $this->router;

        return new React\Http\Server($this->loop,
            function (ServerRequestInterface $request) use ($router): ResponseInterface {
                return $router->dispatch($request);
            });
    }

    /**
     * @param React\Http\Server $server
     */
    private function attachListeners(React\Http\Server $server)
    {
        foreach ($this->eventListeners as $event => $handler) {
            $server->on($event, $handler);
        }
    }

    /**
     * Private function to create TCP socket on the given port
     * @param int $port
     * @return React\Socket\Server
     */
    private function reserveSocket(int $port): React\Socket\Server
    {
        return new React\Socket\Server($port, $this->loop);
    }

    /**
     * Encapsulating function that initialize and starts all the required
     * services before listening on the given port for HTTP calls.
     * @param int $port
     */
    public function listen(int $port = 5000)
    {
        $this->loop = React\EventLoop\Factory::create();
        $server = $this->makeServer();
        $socket = $this->reserveSocket($port);
        $this->attachListeners($server);
        $server->listen($socket);
        $this->loopTimeout();
        $this->loop->run();
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
     * Hook function to pass listeners for events to LoopInterface
     * @param string $event
     * @param callable $callback
     */
    public function on(string $event, callable $callback)
    {
        $this->eventListeners[$event] = $callback;
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

}