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
    private array $eventListeners;
    private LoopInterface $loop;
    private int $loopTimeoutSeconds = 0;

    private function loopTimeout()
    {
        if ($this->loopTimeoutSeconds > 0) {
            $loop = $this->loop;
            $this->loop->addTimer($this->loopTimeoutSeconds, function () use ($loop) {
                echo "Loop timeout enabled, stopping server". PHP_EOL;
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

    private function attachListeners(React\Http\Server $server)
    {
        foreach ($this->eventListeners as $event => $handler) {
            $server->on($event, $handler);
        }
    }

    private function reserveSocket(int $port): React\Socket\Server
    {
        return new React\Socket\Server($port, $this->loop);
    }

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

    public function withTimeout(int $seconds)
    {
        $this->loopTimeoutSeconds = $seconds;

    }


    public function on(string $event, callable $callback)
    {
        $this->eventListeners[$event] = $callback;
    }


    public function withRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }


}