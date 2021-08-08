<?php

declare(strict_types=1);

namespace Tests\Unit\Crow\Router;

use Exception;
use Prophecy\Argument;
use FastRoute\Dispatcher;
use Crow\Router\FastRouter;
use PHPUnit\Framework\TestCase;
use Crow\Router\RouterInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use Crow\Router\FastRouteDispatcher;
use Crow\Handlers\QueueRequestHandler;
use Crow\Handlers\RouteDispatchHandler;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Crow\Router\Exceptions\RoutingLogicException;

class FastRouterTest extends TestCase
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

    public function testPatch()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );
        $router->patch('/patch', function ($request, ResponseInterface $response) {
            return $response->withStatus(303);
        });
        $this->assertEquals(303, $router->dispatch(
            $this->makeRequest('/patch', 'PATCH')
        )->getStatusCode());
    }

    public function testHead()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );
        $router->head('/head', function ($request, ResponseInterface $response) {
            return $response->withStatus(304);
        });
        $this->assertEquals(304, $router->dispatch(
            $this->makeRequest('/head', 'HEAD')
        )->getStatusCode());
    }

    public function testDispatchExceptionHandling()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );

        $router->get('/get', function ($request, ResponseInterface $response) {
            throw new Exception('I am an exception');
        });
        $this->expectException(Exception::class);
        $router->dispatch(
            $this->makeRequest('/get', 'GET')
        );
    }

    public function testDispatchNotFound()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );
        $this->assertEquals(404, $router->dispatch(
            $this->makeRequest('/head', 'HEAD')
        )->getStatusCode());
    }

    public function testDispatchMethodNotAllowed()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );
        $router->get('/get', function ($request, ResponseInterface $response) {
            return $response;
        });
        $this->assertEquals(405, $router->dispatch(
            $this->makeRequest('/get', 'POST')
        )->getStatusCode());
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
        $router = new FastRouter(
            $fastRouteDispatcher->reveal(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );
        $router->get('/get', function ($request, ResponseInterface $response) {
            return $response;
        });
        $this->expectException(RoutingLogicException::class);
        $router->dispatch(
            $this->makeRequest('/get', 'POST')
        );
    }

    public function testDelete()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );
        $router->delete('/delete', function ($request, ResponseInterface $response) {
            return $response->withStatus(305);
        });
        $this->assertEquals(305, $router->dispatch(
            $this->makeRequest('/delete', 'DELETE')
        )->getStatusCode());
    }

    public function testPut()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );
        $router->put('/put', function ($request, ResponseInterface $response) {
            return $response->withStatus(200);
        });
        $this->assertEquals(200, $router->dispatch(
            $this->makeRequest('/put', 'PUT')
        )->getStatusCode());
    }


    public function testAddGroup()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
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
        $this->assertEquals(205, $router->dispatch(
            $this->makeRequest('/group/get', 'GET')
        )->getStatusCode());

        $this->assertEquals(206, $router->dispatch(
            $this->makeRequest('/group/get2', 'GET')
        )->getStatusCode());
    }

    public function testPost()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );
        $router->post('/post', function ($request, ResponseInterface $response) {
            $response->getBody()->write('Hello');

            return $response->withStatus(306)->withHeader('Test', 'TestVal');
        });
        $response = $router->dispatch(
            $this->makeRequest('/post', 'POST')
        );

        $this->assertEquals(306, $response->getStatusCode());
        $this->assertEquals("Hello", $response->getBody()->__toString());
        $this->assertEquals("TestVal", $response->getHeaderLine('Test'));
    }

    public function testAddRoute()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );
        $router->addRoute('POST', '/post', function ($request, ResponseInterface $response) {
            $response->getBody()->write('Hello');

            return $response->withStatus(306)->withHeader('Test', 'TestVal');
        });
        $response = $router->dispatch(
            $this->makeRequest('/post', 'POST')
        );

        $this->assertEquals(306, $response->getStatusCode());
        $this->assertEquals("Hello", $response->getBody()->__toString());
        $this->assertEquals("TestVal", $response->getHeaderLine('Test'));
    }

    public function testGet()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );
        $router->get('/get', function ($request, ResponseInterface $response) {
            return $response->withStatus(200);
        });
        $this->assertEquals(200, $router->dispatch(
            $this->makeRequest('/get', 'GET')
        )->getStatusCode());
    }


    public function testQueryParams()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );
        $router->get('/get', function (RequestInterface $request, ResponseInterface $response) {
            $response->getBody()->write($request->getUri()->getQuery());
            return $response->withStatus(200);
        });
        $response = $router->dispatch(
            $this->makeRequest('/get?foo=bar', 'GET')
        );
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("foo=bar", $response->getBody()->__toString());
    }

    public function testDynamicUrlParams()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );
        $router->get('/get/id/{id}/sunny/{sunny}', function (RequestInterface $request, ResponseInterface $response, $id, $sunny) {
            $response->getBody()->write($id . $sunny);
            return $response->withStatus(200);
        });

        $router->get('/get/id/{id}', function (RequestInterface $request, ResponseInterface $response, $id) {
            $response->getBody()->write($id);
            return $response->withStatus(200);
        });
        $response = $router->dispatch(
            $this->makeRequest('/get/id/1212/sunny/day', 'GET')
        );
        $response2 = $router->dispatch(
            $this->makeRequest('/get/id/1212', 'GET')
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("1212day", $response->getBody()->__toString());
        $this->assertEquals("1212", $response2->getBody()->__toString());
    }

    function testMiddleware()
    {
        $router = new FastRouter(
            new FastRouteDispatcher(),
            new RouteDispatchHandler(new QueueRequestHandler())
        );

        $router->middleware(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
            return $next->handle($request);
        });
        $router->get(
            '/get/id/{id}/sunny/{sunny}',
            function (RequestInterface $request, ResponseInterface $response, $id, $sunny) {
                $response->getBody()->write($id . $sunny);
                return $response->withStatus(200);
            }
        )->middleware(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
                return $next->handle($request);
        });

        $response = $router->dispatch(
            $this->makeRequest('/get/id/1212/sunny/day', 'GET')
        );
        $this->assertEquals(200, $response->getStatusCode());
    }
}
