<?php declare(strict_types=1);

namespace Crow\Http\Server;


use Crow\Handlers\SwooleRequestHandler;
use Crow\Middlewares\UserMiddlewaresList;

final class CrowSwooleServer extends BaseServer
{

    function __construct(private SwoolePHPServer $serverPHPServer,
                         private SwooleRequestHandler $requestHandler,
                         UserMiddlewaresList $middlewaresList
    )
    {
        parent::__construct($middlewaresList);
    }

    public function listen(int $port = 5000, string $host = "127.0.0.1")
    {
        $this->requestHandler->setRouter($this->router);
        $this->requestHandler->setMiddlewaresList($this->middlewaresList);
        $this->server = $this->serverPHPServer->getServer($port, $host);
        $this->attachListeners();
        $this->server->on('request', $this->requestHandler);
        $this->server->start();
    }
}