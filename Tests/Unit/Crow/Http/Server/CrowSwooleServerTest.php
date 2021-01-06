<?php declare(strict_types=1);

namespace Tests\Unit\Crow\Http\Server;

use Crow\Http\Server\CrowSwooleServer;
use Crow\Http\Server\SwoolePHPServer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Swoole;

class CrowSwooleServerTest extends TestCase
{

    private MockObject $swoolePHPServer;
    private MockObject $swooleServer;

    function setup(): void
    {
        $this->swoolePHPServer = $this->getMockBuilder(SwoolePHPServer::class)
            ->disableOriginalConstructor()->getMock();
        $this->swooleServer = $this->getMockBuilder(Swoole\Http\Server::class)
            ->disableOriginalConstructor()->getMock();

    }

    public function testListen()
    {

        $crowSwooleServer = new CrowSwooleServer($this->swoolePHPServer);
        $this->swooleServer->expects($requestSpy = $this->once())
            ->method('on')
            ->with('request');
        $this->swoolePHPServer->expects($serverSpy = $this->once())
            ->method('getServer')
            ->willReturn($this->swooleServer);
        $crowSwooleServer->withTimeout(1);
        $crowSwooleServer->listen();
        $this->assertEquals(1, $serverSpy->getInvocationCount());
    }
}
