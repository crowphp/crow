<?php

declare(strict_types=1);

namespace Crow\Http\Server;

use React;
use Crow\Handlers\ReactRequestHandler;
use Crow\Middlewares\UserMiddlewaresList;
use React\EventLoop\LoopInterface;

final class CrowReactServer extends BaseServer
{

    private ReactPHPServer $reactPHPServer;
    private ReactRequestHandler $requestHandler;

    /**
     * Encapsulating function that initialize and starts all the required
     * services before listening on the given port for HTTP calls.
     * @param ReactPHPServer $reactPHPServer
     * @param ReactRequestHandler $requestHandler
     * @param UserMiddlewaresList $middlewaresList
     */
    public function __construct(
        ReactPHPServer $reactPHPServer,
        ReactRequestHandler $requestHandler,
        UserMiddlewaresList $middlewaresList
    ) {
        $this->reactPHPServer = $reactPHPServer;
        $this->requestHandler = $requestHandler;
        parent::__construct($middlewaresList);
    }

    public function listen(int $port = 5000, string $host = "127.0.0.1"): void
    {
        $this->requestHandler->setRouter($this->router);
        $this->requestHandler->setMiddlewaresList($this->middlewaresList);
        $this->server = $this->reactPHPServer->getServer($this->requestHandler);
        $this->attachListeners();
        $this->server->listen($this->reactPHPServer->getSocket($host . ":" . $port));
        $this->reactPHPServer->getLoop()->run();
    }

    /**
     * @return LoopInterface
     */
    public function getLoop(): LoopInterface
    {
        return $this->reactPHPServer->getLoop();
    }
}
