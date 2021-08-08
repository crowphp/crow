<?php

declare(strict_types=1);

namespace Test\Unit\Crow\Middlewares;

use Crow\Handlers\QueueRequestHandler;
use Crow\Router\Factory as RouterFactory;
use Crow\Middlewares\RoutingMiddleware;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
        $routingMiddleware = new RoutingMiddleware(RouterFactory::make());
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
        $routingMiddleware = new RoutingMiddleware($router);

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
        $routingMiddleware = new RoutingMiddleware(RouterFactory::make());
        $response = $routingMiddleware->process(
            $request,
            $this->prophesize(QueueRequestHandler::class)->reveal()
        );

        $this->assertEquals(true, $response instanceof ResponseInterface);
    }
}
