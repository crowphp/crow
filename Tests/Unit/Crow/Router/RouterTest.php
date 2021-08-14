<?php

declare(strict_types=1);

namespace Tests\Unit\Crow\Router;


use Exception;
use Prophecy\Argument;
use FastRoute\Dispatcher;
use Crow\Router\Router;
use PHPUnit\Framework\TestCase;
use Crow\Router\RouterInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use Crow\Router\FastRouteDispatcher;
use Crow\Handlers\QueueRequestHandler;
use Crow\Handlers\RouteDispatchHandler;
use Psr\Http\Message\RequestInterface;
use Crow\Middlewares\RoutingMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Crow\Router\Types\RouteMiddlewareCollection;

class RouterTest extends TestCase
{
    use ProphecyTrait;

    private RequestHandlerInterface $requestHandler;

    public function setup(): void
    {
        parent::setup();
        $this->requestHandler = $this->prophesize(RequestHandlerInterface::class)->reveal();
    }

    private function makeRoutingMiddleware(Router $router): RoutingMiddleware
    {
        return new RoutingMiddleware(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler()),
            $router
        );
    }

    private function makeRequest($uri, $method): ServerRequestInterface
    {
        $requestFactory = new ServerRequestFactory();
        return $requestFactory->createServerRequest(
            $method,
            $uri
        );
    }

    public function testPatch()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );
        $router->patch('/patch', function ($request, ResponseInterface $response) {
            return $response->withStatus(303);
        });
        $handler = $this->makeRoutingMiddleware($router);
        $this->assertEquals(303, $handler->process(
            $this->makeRequest('/patch', 'PATCH'),
            $this->requestHandler
        )->getStatusCode());
    }

    public function testHead()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );
        $router->head('/head', function ($request, ResponseInterface $response) {
            return $response->withStatus(304);
        });
        $handler = $this->makeRoutingMiddleware($router);
        $this->assertEquals(304, $handler->process(
            $this->makeRequest('/head', 'HEAD'),
            $this->requestHandler
        )->getStatusCode());
    }

    public function testDispatchExceptionHandling()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );

        $router->get('/get', function ($request, ResponseInterface $response) {
            throw new Exception('I am an exception');
        });
        $handler = $this->makeRoutingMiddleware($router);
        $this->expectException(Exception::class);
        $handler->process(
            $this->makeRequest('/get', 'GET'),
            $this->requestHandler
        );
    }

    public function testDispatchNotFound()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );
        $handler = $this->makeRoutingMiddleware($router);
        $this->assertEquals(404, $handler->process(
            $this->makeRequest('/head', 'HEAD'),
            $this->requestHandler
        )->getStatusCode());
    }

    public function testDispatchMethodNotAllowed()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );
        $router->get('/get', function ($request, ResponseInterface $response) {
            return $response;
        });
        $handler = $this->makeRoutingMiddleware($router);
        $this->assertEquals(405, $handler->process(
            $this->makeRequest('/get', 'POST'),
            $this->requestHandler
        )->getStatusCode());
    }


    public function testDelete()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );
        $router->delete('/delete', function ($request, ResponseInterface $response) {
            return $response->withStatus(305);
        });
        $handler = $this->makeRoutingMiddleware($router);
        $this->assertEquals(305, $handler->process(
            $this->makeRequest('/delete', 'DELETE'),
            $this->requestHandler
        )->getStatusCode());
    }

    public function testPut()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );
        $router->put('/put', function ($request, ResponseInterface $response) {
            return $response->withStatus(200);
        });
        $handler = $this->makeRoutingMiddleware($router);
        $this->assertEquals(200, $handler->process(
            $this->makeRequest('/put', 'PUT'),
            $this->requestHandler
        )->getStatusCode());
    }


    public function testAddGroup()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );
        $router->addGroup('/group', function (RouterInterface $router) {
            $router->get('/get', function ($request, ResponseInterface $response) {
                return $response->withStatus(205);
            });
            $router->get('/get2', function ($request, ResponseInterface $response) {
                return $response->withStatus(206);
            });
        }, function (ServerRequestInterface $request, RequestHandlerInterface $next) {
            return $next->handle($request);
        });
        $handler = $this->makeRoutingMiddleware($router);
        $this->assertEquals(205, $handler->process(
            $this->makeRequest('/group/get', 'GET'),
            $this->requestHandler
        )->getStatusCode());

        $this->assertEquals(206, $handler->process(
            $this->makeRequest('/group/get2', 'GET'),
            $this->requestHandler
        )->getStatusCode());
    }

    public function testPost()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );
        $router->post('/post', function ($request, ResponseInterface $response) {
            $response->getBody()->write('Hello');

            return $response->withStatus(306)->withHeader('Test', 'TestVal');
        });
        $handler = $this->makeRoutingMiddleware($router);
        $response = $handler->process(
            $this->makeRequest('/post', 'POST'),
            $this->requestHandler
        );

        $this->assertEquals(306, $response->getStatusCode());
        $this->assertEquals("Hello", $response->getBody()->__toString());
        $this->assertEquals("TestVal", $response->getHeaderLine('Test'));
    }

    public function testAddRoute()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );
        $router->addRoute('POST', '/post', function ($request, ResponseInterface $response) {
            $response->getBody()->write('Hello');

            return $response->withStatus(306)->withHeader('Test', 'TestVal');
        });
        $handler = $this->makeRoutingMiddleware($router);
        $response = $handler->process(
            $this->makeRequest('/post', 'POST'),
            $this->requestHandler
        );

        $this->assertEquals(306, $response->getStatusCode());
        $this->assertEquals("Hello", $response->getBody()->__toString());
        $this->assertEquals("TestVal", $response->getHeaderLine('Test'));
    }

    public function testGet()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );
        $router->get('/get', function ($request, ResponseInterface $response) {
            return $response->withStatus(200);
        });
        $handler = $this->makeRoutingMiddleware($router);
        $this->assertEquals(200, $handler->process(
            $this->makeRequest('/get', 'GET'),
            $this->requestHandler
        )->getStatusCode());
    }


    public function testQueryParams()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );
        $router->get('/get', function (RequestInterface $request, ResponseInterface $response) {
            $response->getBody()->write($request->getUri()->getQuery());
            return $response->withStatus(200);
        });
        $handler = $this->makeRoutingMiddleware($router);
        $response = $handler->process(
            $this->makeRequest('/get?foo=bar', 'GET'),
            $this->requestHandler
        );
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("foo=bar", $response->getBody()->__toString());
    }

    public function testDynamicUrlParams()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );
        $router->get('/get/id/{id}/sunny/{sunny}', function (RequestInterface $request, ResponseInterface $response, $id, $sunny) {
            $response->getBody()->write($id . $sunny);
            return $response->withStatus(200);
        });

        $router->get('/get/id/{id}', function (RequestInterface $request, ResponseInterface $response, $id) {
            $response->getBody()->write($id);
            return $response->withStatus(200);
        });
        $handler = $this->makeRoutingMiddleware($router);
        $response = $handler->process(
            $this->makeRequest('/get/id/1212/sunny/day', 'GET'),
            $this->requestHandler
        );
        $response2 = $handler->process(
            $this->makeRequest('/get/id/1212', 'GET'),
            $this->requestHandler
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("1212day", $response->getBody()->__toString());
        $this->assertEquals("1212", $response2->getBody()->__toString());
    }

    function testMiddleware()
    {
        $router = new Router(
            new RouteMiddlewareCollection()
        );

        $router->get(
            '/get/id/{id}/sunny/{sunny}',
            function (RequestInterface $request, ResponseInterface $response, $id, $sunny) {
                $response->getBody()->write($id . $sunny);
                if($request->hasHeader('test') && $request->getHeaderLine('test') === "value"){
                    return $response->withStatus(200)
                        ->withHeader("test", $request->getHeader('test'));
                }
                return $response->withStatus(500);
            }
        )->middleware(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
            return $next->handle($request->withHeader("test","value"));
        });

        $handler = $this->makeRoutingMiddleware($router);

        $response = $handler->process(
            $this->makeRequest('/get/id/1212/sunny/day', 'GET'),
            $this->requestHandler
        );
        $this->assertEquals(200, $response->getStatusCode());
    }
}
