<?php
declare(strict_types=1);

namespace Crow\Http\Server;

use Swoole\Http\Request;
use Swoole\Http\Server;
use Swoole\Http\Response;
use Crow\Http\RequestFactory;
use Crow\Http\PsrToSwooleResponseBuilder;

final class SwooleServer extends BaseServer
{

    public function listen(int $port = 5000, string $host = "127.0.0.1")
    {
        $this->server = new Server($host, $port);
        $app = $this;
        $this->attachListeners();
        $this->server->on('request', function (Request $request, Response $response) use ($app) {
            $responseMerger = new PsrToSwooleResponseBuilder();
            $responseMerger->toSwoole(
                $app->makeMiddlewareHandlerForRequest()->handle(
                    RequestFactory::create($request)
                ),
                $response
            )->end();
        });
        $this->loopTimeout();
        $this->server->start();

    }

    private function loopTimeout()
    {
        if ($this->loopTimeoutSeconds > 0) {
            $app = $this;
            $this->server->tick($this->loopTimeoutSeconds * 1000, function () use ($app) {
                echo "Loop timeout enabled, stopping server" . PHP_EOL;
                $app->server->stop();
            });
        }
    }
}