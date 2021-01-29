<?php declare(strict_types=1);

namespace Tests\Unit\Crow\Handlers;

use Crow\Handlers\QueueRequestHandler;
use Crow\Handlers\RouteDispatchHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteDispatchHandlerTest extends TestCase
{
    function test__invoke(): void
    {
        $request = $this->getMockForAbstractClass(ServerRequestInterface::class);
        $routeDispatchHandler = new RouteDispatchHandler(
            new QueueRequestHandler()
        );
        $middlewares = array(
          function(ServerRequestInterface $request, RequestHandlerInterface $handler){
              return $handler->handle($request);
          }
        );
        $response = call_user_func(
            $routeDispatchHandler,
            $middlewares,
            function(ServerRequestInterface $request, ResponseInterface $response){
                return $response;
            },
            $request,
            []
        );
        $this->assertInstanceOf(ResponseInterface::class, $response);

    }
}
