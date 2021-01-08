<?php declare(strict_types=1);

namespace Tests\Unit\Crow\Http\Server;

use Crow\Handlers\ReactRequestHandler;
use Crow\Http\Server\CrowReactServer;
use Crow\Http\Server\Exceptions\InvalidEventType;
use Crow\Http\Server\ReactPHPServer;
use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\RouterInterface;
use React\Http\Server;
use React\Socket\Server as Socket;
use React\EventLoop\LoopInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class CrowReactServerTest extends TestCase
{
    use ProphecyTrait;

    private MockObject $reactPHPServer;
    private MockObject $reactRequestHandler;
    private MockObject $middlewaresList;
    private MockObject $router;
    private MockObject $loop;

    function setup(): void
    {
        $this->reactPHPServer = $this->getMockBuilder(ReactPHPServer::class)
            ->disableOriginalConstructor()->getMock();
        $this->reactRequestHandler = $this->getMockBuilder(ReactRequestHandler::class)
            ->disableOriginalConstructor()->getMock();
        $this->middlewaresList = $this->getMockBuilder(UserMiddlewaresList::class)
            ->disableOriginalConstructor()->getMock();
        $this->router = $this->getMockForAbstractClass(RouterInterface::class);
        $this->loop = $this->getMockForAbstractClass(LoopInterface::class);
    }

    public function testGetLoop()
    {
        $this->reactPHPServer->method('getLoop')->willReturn($this->loop);
        $crowReactServer = new CrowReactServer(
            $this->reactPHPServer,
            $this->reactRequestHandler,
            $this->middlewaresList
        );
        $crowReactServer->withRouter($this->router);
        $this->assertTrue($crowReactServer->getLoop() instanceof LoopInterface);
    }

    public function testListen()
    {
        $this->reactPHPServer->method('getLoop')->willReturn($this->loop);
        $crowReactServer = new CrowReactServer(
            $this->reactPHPServer,
            $this->reactRequestHandler,
            $this->middlewaresList
        );
        $crowReactServer->on('error', function () {
        });
        $crowReactServer->withRouter($this->router);
        $this->reactPHPServer->expects($serverSpy = $this->once())
            ->method('getServer')
            ->willReturn(new Server($this->loop, function () {
            }));
        $this->reactPHPServer->expects($socketSpy = $this->once())
            ->method('getSocket')
            ->willReturn(new Socket("127.0.0.1:5005", $this->loop));
        $crowReactServer->withTimeout(1);
        $crowReactServer->listen();
        $this->assertEquals(1, $serverSpy->getInvocationCount());
        $this->assertEquals(1, $socketSpy->getInvocationCount());

    }

    public function testInvalidEventType()
    {
        $this->reactPHPServer->method('getLoop')->willReturn($this->loop);
        $crowReactServer = new CrowReactServer(
            $this->reactPHPServer,
            $this->reactRequestHandler,
            $this->middlewaresList
        );
        $this->expectException(InvalidEventType::class);
        $crowReactServer->on('request', function () {
        });
        
    }

}
