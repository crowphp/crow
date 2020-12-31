<?php declare(strict_types=1);

namespace Test\Unit\Crow\Router;

use Crow\Router\FastRouter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Request;
use Exception;

class FastRouterTest extends TestCase
{

    public function testPatch()
    {
        $router = new FastRouter();
        $router->patch('/patch', function ($request, ResponseInterface $response) {
            return $response->withStatus(303);
        });
        $this->assertEquals(303, $router->dispatch(
            new Request(
                '/patch',
                'PATCH',
                "php://memory"
            )
        )->getStatusCode());
    }

    public function testHead()
    {
        $router = new FastRouter();
        $router->head('/head', function ($request, ResponseInterface $response) {
            return $response->withStatus(304);
        });
        $this->assertEquals(304, $router->dispatch(
            new Request(
                '/head',
                'HEAD',
                "php://memory"
            )
        )->getStatusCode());
    }

    public function testDispatchExceptionHandling()
    {
        $router = new FastRouter();
        $router->get('/get', function ($request, ResponseInterface $response) {
            throw new Exception('I am an exception');
        });
        $this->assertEquals(500, $router->dispatch(
            new Request(
                '/get',
                'GET',
                "php://memory"
            )
        )->getStatusCode());
    }

    public function testDispatchNotFound()
    {
        $router = new FastRouter();
        $this->assertEquals(404, $router->dispatch(
            new Request(
                '/get',
                'GET',
                "php://memory"
            )
        )->getStatusCode());
    }

    public function testDispatchMethodNotAllowed()
    {
        $router = new FastRouter();
        $router->get('/get', function ($request, ResponseInterface $response) {
            return $response;
        });
        $this->assertEquals(405, $router->dispatch(
            new Request(
                '/get',
                'POST',
                "php://memory"
            )
        )->getStatusCode());
    }

    public function testDelete()
    {
        $router = new FastRouter();
        $router->delete('/delete', function ($request, ResponseInterface $response) {
            return $response->withStatus(305);
        });
        $this->assertEquals(305, $router->dispatch(
            new Request(
                '/delete',
                'DELETE',
                "php://memory"
            )
        )->getStatusCode());
    }

    public function testPost()
    {
        $router = new FastRouter();
        $router->post('/post', function ($request, ResponseInterface $response) {
            $response->getBody()->write('Hello');

            return $response->withStatus(306)->withHeader('Test', 'TestVal');
        });
        $response = $router->dispatch(
            new Request(
                '/post',
                'POST',
                "php://memory"
            )
        );

        $this->assertEquals(306, $response->getStatusCode());
        $this->assertEquals("Hello", $response->getBody()->__toString());
        $this->assertEquals("TestVal", $response->getHeaderLine('Test'));
    }

    public function testAddRoute()
    {
        $router = new FastRouter();
        $router->addRoute('POST', '/post', function ($request, ResponseInterface $response) {
            $response->getBody()->write('Hello');

            return $response->withStatus(306)->withHeader('Test', 'TestVal');
        });
        $response = $router->dispatch(
            new Request(
                '/post',
                'POST',
                "php://memory"
            )
        );

        $this->assertEquals(306, $response->getStatusCode());
        $this->assertEquals("Hello", $response->getBody()->__toString());
        $this->assertEquals("TestVal", $response->getHeaderLine('Test'));

    }

    public function testGet()
    {
        $router = new FastRouter();
        $router->get('/get', function ($request, ResponseInterface $response) {
            return $response->withStatus(200);
        });
        $this->assertEquals(200, $router->dispatch(
            new Request(
                '/get',
                'GET',
                "php://memory"
            )
        )->getStatusCode());
    }


    public function testQueryParams()
    {
        $router = new FastRouter();
        $router->get('/get', function (RequestInterface $request, ResponseInterface $response) {
            $response->getBody()->write($request->getUri()->getQuery());
            return $response->withStatus(200);
        });
        $response = $router->dispatch(
            new Request(
                '/get?foo=bar',
                'GET',
                "php://memory"
            )
        );
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("foo=bar", $response->getBody()->__toString());
    }

    public function testDynamicUrlParams()
    {
        $router = new FastRouter();
        $router->get('/get/id/{id}/sunny/{sunny}', function (RequestInterface $request, ResponseInterface $response, $id, $sunny) {
            $response->getBody()->write($id . $sunny);
            return $response->withStatus(200);
        });

        $router->get('/get/id/{id}', function (RequestInterface $request, ResponseInterface $response, $id) {
            $response->getBody()->write($id);
            return $response->withStatus(200);
        });
        $response = $router->dispatch(
            new Request(
                '/get/id/1212/sunny/day',
                'GET',
                "php://memory"
            )
        );
        $response2 = $router->dispatch(
            new Request(
                '/get/id/1212',
                'GET',
                "php://memory"
            )
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("1212day", $response->getBody()->__toString());
        $this->assertEquals("1212", $response2->getBody()->__toString());
    }
}
