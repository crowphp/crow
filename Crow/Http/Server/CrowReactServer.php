<?php declare(strict_types=1);

namespace Crow\Http\Server;

use React;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;

final class CrowReactServer extends BaseServer
{

    /**
     * Encapsulating function that initialize and starts all the required
     * services before listening on the given port for HTTP calls.
     * @param ReactPHPServer $reactPHPServer
     */
    public function __construct(private ReactPHPServer $reactPHPServer)
    {
    }

    public function listen(int $port = 5000, string $host = "127.0.0.1")
    {
        $this->server = $this->makeServer();
        $this->attachListeners();
        $this->server->listen($this->reactPHPServer->getSocket($host . ":" . $port));
        $this->loopTimeout();
        $this->reactPHPServer->getLoop()->run();
    }

    private function loopTimeout()
    {
        if ($this->loopTimeoutSeconds > 0) {
            $loop = $this->reactPHPServer->getLoop();
            $this->reactPHPServer->getLoop()->addTimer($this->loopTimeoutSeconds, function () use ($loop) {
                echo "Loop timeout enabled, stopping server" . PHP_EOL;
                $loop->stop();
            });
        }
    }

    private function makeServer(): React\Http\Server
    {
        $app = $this;
        return $this->reactPHPServer->getServer(
            function (ServerRequestInterface $request) use ($app) {
                return $app->makeMiddlewareHandlerForRequest()->handle($request);
            }
        );
    }

    /**
     * @return LoopInterface
     */
    public function getLoop(): LoopInterface
    {
        return $this->reactPHPServer->getLoop();
    }

}