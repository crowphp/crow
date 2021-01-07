<?php

namespace Tests\Unit\Crow\Http\Server;

use Crow\Http\Server\ReactPHPServer;
use PHPUnit\Framework\TestCase;
use React;

class ReactPHPServerTest extends TestCase
{

    /**
     * @var ReactPHPServer
     */
    private ReactPHPServer $reactPHPserver;

    function setup(): void
    {
        $this->reactPHPserver = new ReactPHPServer();
    }

    public function testGetServer()
    {
        $this->assertTrue(
            $this->reactPHPserver->getServer(function () {
            }) instanceof React\Http\Server
        );
    }

    public function testGetSocket()
    {
        $socket = $this->reactPHPserver->getSocket('127.0.0.1:5002');
        $socket->close();
        $this->assertTrue(
            $socket instanceof React\Socket\Server
        );
    }

    public function testGetLoop()
    {
        $this->assertTrue(
            $this->reactPHPserver->getLoop() instanceof React\EventLoop\LoopInterface
        );
    }
}
