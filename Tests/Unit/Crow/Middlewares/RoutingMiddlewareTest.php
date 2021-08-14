<?php

declare(strict_types=1);

namespace Test\Unit\Crow\Middlewares;

use Crow\Handlers\QueueRequestHandler;
use Crow\Handlers\RouteDispatchHandler;
use Crow\Router\DispatcherFactoryInterface;
use Crow\Router\Exceptions\RoutingLogicException;
use Crow\Router\Factory as RouterFactory;
use Crow\Middlewares\RoutingMiddleware;
use Crow\Router\FastRouteDispatcher;
use Crow\Router\Router;
use Crow\Router\Types\RouteMiddlewareCollection;
use FastRoute\Dispatcher;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutingMiddlewareTest extends TestCase
{
    use ProphecyTrait;


    private function makeRequest($uri, $method): ServerRequestInterface
    {
        $requestFactory = new ServerRequestFactory();
        return $requestFactory->createServerRequest(
            $method,
            $uri
        );
    }

    public function testIfRoutingMiddlewareCalledWithoutAnyRoutesShouldReturnNotFound()
    {
        $request = ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
        $routingMiddleware = new RoutingMiddleware(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler()),
            RouterFactory::make()
        );
        $response = $routingMiddleware->process(
            $request,
            $this->prophesize(QueueRequestHandler::class)->reveal()
        );

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("Not Found", $response->getBody());
        $this->assertEquals(true, $response instanceof ResponseInterface);
    }

    public function testRoutingMiddlewareWith200GetResponse()
    {
        $router = RouterFactory::make();
        $router->get('/', function ($request, ResponseInterface $response) {
            $response->getBody()->write('Hello');
            return $response->withStatus(200);
        });
        $routingMiddleware = new RoutingMiddleware(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler()),
            $router
        );

        $response = $routingMiddleware->process(
            $this->makeRequest('/', 'GET'),
            $this->prophesize(QueueRequestHandler::class)->reveal()
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Hello", $response->getBody()->__toString());
        $this->assertEquals(true, $response instanceof ResponseInterface);
    }

    public function testRoutingMiddlewareReturnsResponseObject()
    {
        $request = ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
        $routingMiddleware = new RoutingMiddleware(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler()),
            RouterFactory::make()
        );
        $response = $routingMiddleware->process(
            $request,
            $this->prophesize(QueueRequestHandler::class)->reveal()
        );

        $this->assertEquals(true, $response instanceof ResponseInterface);
    }

    public function testDispatchRoutingLogicException()
    {
        $fastRouteDispatcher = $this->prophesize(FastRouteDispatcher::class);
        $fastRouteDispatcher->make(Argument::any())
            ->shouldBeCalled()
            ->willReturn(new class implements Dispatcher {

                public function dispatch($httpMethod, $uri)
                {
                    return [100];
                }
            });

        $router = new Router(
            new RouteMiddlewareCollection()
        );

        $router->get('/get', function ($request, ResponseInterface $response) {
            return $response;
        });
        $this->expectException(RoutingLogicException::class);
        $routingMiddleware = new RoutingMiddleware(
            $fastRouteDispatcher->reveal(),
            new RouteDispatchHandler(new QueueRequestHandler()),
            RouterFactory::make()
        );
        $routingMiddleware->process(
            $this->makeRequest('/get', 'POST'),
            $this->prophesize(RequestHandlerInterface::class)->reveal()
        );
    }
}
