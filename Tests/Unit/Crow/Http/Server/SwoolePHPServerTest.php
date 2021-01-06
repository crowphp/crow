<?php

namespace Tests\Unit\Crow\Http\Server;

use Crow\Http\Server\SwoolePHPServer;
use PHPUnit\Framework\TestCase;
use Swoole\Http\Server;

class SwoolePHPServerTest extends TestCase
{

    public function testGetServer()
    {
        $server = new SwoolePHPServer();
        $this->assertTrue($server->getServer(5005, "127.0.0.1") instanceof Server);
    }
}
