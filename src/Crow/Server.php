<?php


namespace Crow;

use Crow\Router\RouterInterface;
use React;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;

class Server
{

    private RouterInterface $router;
    private array $eventListeners;

    public function listen(int $port = 5000)
    {
        $loop = React\EventLoop\Factory::create();
        $server = $this->makeServer($loop);
        $socket = $this->reserveSocket($port, $loop);
        $this->attachListeners($server);
        $server->listen($socket);
        $loop->run();
    }

    private function makeServer(LoopInterface $loop): React\Http\Server
    {
        $router = $this->router;

        return new React\Http\Server($loop,
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

    public function on(string $event, callable $callback)
    {
        $this->eventListeners[$event] = $callback;
    }

    private function reserveSocket(int $port, LoopInterface $loop): React\Socket\Server
    {
        return new React\Socket\Server($port, $loop);
    }

    public function withRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }


}