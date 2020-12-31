<?php declare(strict_types=1);

namespace Crow\Http\Server;

use React;
use Crow\Router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;

final class ReactServer extends BaseServer
{


    private LoopInterface $loop;

    /**
     * Encapsulating function that initialize and starts all the required
     * services before listening on the given port for HTTP calls.
     * @param int $port
     * @param string $host
     */

    public function listen(int $port = 5000, string $host = "127.0.0.1")
    {
        $this->loop = React\EventLoop\Factory::create();
        $this->server = $this->makeServer();
        $socket = $this->reserveSocket($port);
        $this->attachListeners();
        $this->server->listen($socket);
        $this->loopTimeout();
        $this->loop->run();
    }

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
        $app = $this;
        return new React\Http\Server($this->loop,
            function (ServerRequestInterface $request) use ($app) {
                return $app->makeMiddlewareHandlerForRequest()->handle($request);
            }
        );
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
     * @return LoopInterface
     */
    public function getLoop(): LoopInterface
    {
        return $this->loop;
    }


}