<?php declare(strict_types=1);

namespace Crow\Http\Server;

use React\EventLoop\LoopInterface;
use React;

class ReactPHPServer
{
    private LoopInterface $loop;

    public function __construct()
    {
        $this->loop = React\EventLoop\Factory::create();
    }

    public function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    public function getServer(callable $handler): React\Http\Server
    {
        return new React\Http\Server($this->loop, $handler);
    }

    public function getSocket(string $uri): React\Socket\Server
    {
        return new React\Socket\Server($uri, $this->loop);
    }
}