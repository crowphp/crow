<?php

declare(strict_types=1);

namespace Tests\Unit\Crow\Handlers;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use PHPUnit\Framework\TestCase;
use Crow\Handlers\QueueRequestHandler;
use Crow\Handlers\RouteDispatchHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;


class RouteDispatchHandlerTest extends TestCase
{
    use ProphecyTrait;

    function test__invoke(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->hasHeader(Argument::any())->willReturn(true);
        $request->getHeader(Argument::any())->willReturn("crowphp");

        $routeDispatchHandler = new RouteDispatchHandler(new QueueRequestHandler());
        $middlewares = [
            function (ServerRequestInterface $request, RequestHandlerInterface $handler) {

                return $handler->handle($request);
            }
        ];
        $response = call_user_func($routeDispatchHandler, $middlewares, function (ServerRequestInterface $request, ResponseInterface $response) {

                return $response;
        }, $request->reveal(), []);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
