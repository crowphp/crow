<?php

declare(strict_types=1);

namespace Crow\Http\Server;

use Crow\Handlers\SwooleRequestHandler;
use Crow\Middlewares\UserMiddlewaresList;

final class CrowSwooleServer extends BaseServer
{

    private SwoolePHPServer $swoolePHPServer;
    private SwooleRequestHandler $requestHandler;


    public function __construct(
        SwoolePHPServer $swoolePHPServer,
        SwooleRequestHandler $requestHandler,
        UserMiddlewaresList $middlewaresList
    ) {
        $this->swoolePHPServer = $swoolePHPServer;
        $this->requestHandler = $requestHandler;
        parent::__construct($middlewaresList);
    }

    public function listen(int $port = 5000, string $host = "127.0.0.1"): void
    {
        $this->requestHandler->setRouter($this->router);
        $this->requestHandler->setMiddlewaresList($this->middlewaresList);
        $this->server = $this->swoolePHPServer->getServer($port, $host);
        $this->attachListeners();
        $this->server->on('request', $this->requestHandler);
        $this->setServerSettings();
        $this->server->start();
    }

    private function setServerSettings(): void
    {
        if (count($this->configs) > 0) {
            $this->server->set($this->configs);
        }
    }
}
