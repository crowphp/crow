<?php

namespace Tests\Unit\Crow\Http\Server;

use Crow\Http\Server\SwoolePHPServer;
use PHPUnit\Framework\TestCase;
use Swoole\Http\Server;

class SwoolePHPServerTest extends TestCase
{

    public function testGetServer()
    {
        $swoolePHPServer = new SwoolePHPServer();
        $server = $swoolePHPServer->getServer(5002, "127.0.0.1");
        $this->assertTrue($server instanceof Server);

    }
}
