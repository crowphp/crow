<?php declare(strict_types=1);

namespace Tests\Unit\Crow\Http\Server;

use Crow\Handlers\SwooleRequestHandler;
use Crow\Http\Server\CrowSwooleServer;
use Crow\Http\Server\Exceptions\InvalidEventType;
use Crow\Http\Server\SwoolePHPServer;
use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\RouterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Swoole;

class CrowSwooleServerTest extends TestCase
{

    private MockObject $swoolePHPServer;
    private MockObject $swooleServer;
    private MockObject $swooleRequestHandler;
    private MockObject $router;

    function setup(): void
    {
        $this->swoolePHPServer = $this->getMockBuilder(SwoolePHPServer::class)
            ->disableOriginalConstructor()->getMock();
        $this->swooleServer = $this->getMockBuilder(Swoole\Http\Server::class)
            ->disableOriginalConstructor()->getMock();
        $this->swooleRequestHandler = $this->getMockBuilder(SwooleRequestHandler::class)
            ->disableOriginalConstructor()->getMock();
        $this->router = $this->getMockForAbstractClass(RouterInterface::class);

    }

    public function testListen()
    {
        $crowSwooleServer = new CrowSwooleServer(
            $this->swoolePHPServer,
            $this->swooleRequestHandler,
            new UserMiddlewaresList());
        $crowSwooleServer->withRouter($this->router);
        $crowSwooleServer->on('error', function () {
        });

        $crowSwooleServer->use(function () {
        });
        $this->swooleServer->expects($requestSpy = $this->atLeastOnce())
            ->method('on');
        $this->swoolePHPServer->expects($serverSpy = $this->once())
            ->method('getServer')
            ->willReturn($this->swooleServer);
        $crowSwooleServer->withTimeout(1);
        $crowSwooleServer->listen();
        $this->assertEquals(1, $serverSpy->getInvocationCount());
    }

    public function testInvalidEventType()
    {
        $crowSwooleServer = new CrowSwooleServer(
            $this->swoolePHPServer,
            $this->swooleRequestHandler,
            new UserMiddlewaresList());
        $this->expectException(InvalidEventType::class);
        $crowSwooleServer->on('request', function () {
        });
    }
}
