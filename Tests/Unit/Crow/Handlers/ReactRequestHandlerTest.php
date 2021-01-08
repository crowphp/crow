<?php

namespace Tests\Unit\Crow\Handlers;

use Crow\Handlers\QueueRequestHandlerBuilder;
use Crow\Handlers\ReactRequestHandler;
use Crow\Http\RequestFactory;
use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\RouterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;


class ReactRequestHandlerTest extends TestCase
{

    private MockObject $queueRequestHandler;
    private MockObject $router;
    private MockObject $request;
    private MockObject $requestFactory;

    function setup(): void
    {
        $this->queueRequestHandler = $this->getMockBuilder(QueueRequestHandlerBuilder::class)
            ->disableOriginalConstructor()->getMock();
        $this->router = $this->getMockForAbstractClass(RouterInterface::class);
        $this->request = $this->getMockForAbstractClass(ServerRequestInterface::class);
        $this->requestFactory = $this->getMockBuilder(RequestFactory::class)
            ->disableOriginalConstructor()->getMock();

    }


    public function test__invoke()
    {
        $this->requestFactory->expects($toRequestSpy = $this->once())
            ->method('create');
        $reactRequestHandler = new ReactRequestHandler(
            $this->queueRequestHandler,
            $this->requestFactory
        );
        $userMiddlewares = new UserMiddlewaresList();
        $userMiddlewares->add(function () {
        });
        $reactRequestHandler->setMiddlewaresList($userMiddlewares);
        $reactRequestHandler->setRouter($this->router);
        call_user_func($reactRequestHandler, $this->request);
        $this->assertEquals(1, $toRequestSpy->getInvocationCount());
    }
}
