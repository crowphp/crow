<?php declare(strict_types=1);

namespace Tests\Unit\Crow\Handlers;

use Crow\Handlers\QueueRequestHandlerBuilder;
use Crow\Handlers\SwooleRequestHandler;
use Crow\Http\PsrToSwooleResponseBuilder;
use Crow\Http\RequestFactory;
use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\RouterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Swoole\Http\Request;
use Swoole\Http\Response;

class SwooleRequestHandlerTest extends TestCase
{

    private MockObject $queueRequestHandler;
    private MockObject $router;
    private MockObject $request;
    private MockObject $response;
    private MockObject $psrToSwooleResponseBuilder;
    private MockObject $swooleRequest;

    function setup(): void
    {
        $this->queueRequestHandler = $this->getMockBuilder(QueueRequestHandlerBuilder::class)
            ->disableOriginalConstructor()->getMock();
        $this->router = $this->getMockForAbstractClass(RouterInterface::class);

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()->getMock();

        $this->response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()->getMock();

        $this->psrToSwooleResponseBuilder = $this->getMockBuilder(PsrToSwooleResponseBuilder::class)
            ->disableOriginalConstructor()->getMock();

        $this->swooleRequest = $this->getMockBuilder(RequestFactory::class)
            ->disableOriginalConstructor()->getMock();

    }


    public function test__invoke()
    {
        $this->psrToSwooleResponseBuilder->expects($toSwooleSpy = $this->once())
            ->method('toSwoole');
        $this->swooleRequest->expects($toRequestSpy = $this->once())
            ->method('create');
        $swooleRequestHandler = new SwooleRequestHandler(
            $this->queueRequestHandler,
            $this->psrToSwooleResponseBuilder,
            $this->swooleRequest
        );
        $userMiddlewares = new UserMiddlewaresList();
        $userMiddlewares->add(function () {

        });
        $swooleRequestHandler->setMiddlewaresList(new UserMiddlewaresList());
        $swooleRequestHandler->setRouter($this->router);
        call_user_func($swooleRequestHandler, $this->request, $this->response);

        $this->assertEquals(1, $toRequestSpy->getInvocationCount());
        $this->assertEquals(1, $toSwooleSpy->getInvocationCount());
    }


}
