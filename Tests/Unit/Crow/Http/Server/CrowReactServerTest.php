<?php declare(strict_types=1);

namespace Tests\Unit\Crow\Http\Server;

use Crow\Http\Server\CrowReactServer;
use Crow\Http\Server\ReactPHPServer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Server;
use React\Socket\Server as Socket;

class CrowReactServerTest extends TestCase
{
    use ProphecyTrait;

    private MockObject $reactPHPServer;

    function setup(): void
    {
        $this->reactPHPServer = $this->getMockBuilder(ReactPHPServer::class)
            ->disableOriginalConstructor()->getMock();;

    }

    public function testGetLoop()
    {
        $this->reactPHPServer->method('getLoop')->willReturn(Factory::create());
        $crowReactServer = new CrowReactServer($this->reactPHPServer);
        $this->assertTrue($crowReactServer->getLoop() instanceof LoopInterface);
    }

    public function testListen()
    {
        $this->reactPHPServer->method('getLoop')->willReturn(Factory::create());
        $crowReactServer = new CrowReactServer($this->reactPHPServer);
        $this->reactPHPServer->expects($serverSpy = $this->once())
            ->method('getServer')
            ->willReturn(new Server(Factory::create(), function () {
            }));
        $this->reactPHPServer->expects($socketSpy = $this->once())
            ->method('getSocket')
            ->willReturn(new Socket("127.0.0.1:5005",Factory::create()));
        $crowReactServer->withTimeout(1);
        $crowReactServer->listen();
        $this->assertEquals(1, $serverSpy->getInvocationCount());
        $this->assertEquals(1, $socketSpy->getInvocationCount());

    }

}
