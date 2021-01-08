<?php

namespace Tests\Unit\Crow\Handlers;

use Crow\Handlers\QueueRequestHandler;
use Crow\Handlers\QueueRequestHandlerBuilder;
use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\RouterInterface;
use PHPUnit\Framework\TestCase;

class QueueRequestHandlerBuilderTest extends TestCase
{

    public function testBuild()
    {
        $queueRequestHandlerBuilder = new QueueRequestHandlerBuilder();

        $middlewaresList = new UserMiddlewaresList();
        $middlewaresList->add(function () {
        });
        $this->assertTrue($queueRequestHandlerBuilder->build(
                $middlewaresList,
                $this->getMockForAbstractClass(RouterInterface::class)
            ) instanceof QueueRequestHandler);
    }
}
