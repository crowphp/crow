<?php declare(strict_types=1);

namespace Test\Unit\Crow\Http;

use Crow\Http\RequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request as SwooleReq;
use React\Http\Message\ServerRequest as ReactReq;

class RequestFactoryTest extends TestCase
{

    public function testShouldReturnRequestInterfaceWhenSwooleRequestGivenToCreate()
    {

        $swoole = new SwooleReq();
        $swoole->server['request_uri'] = "/api";
        $swoole->server['request_method'] = "GET";
        $swoole->server['server_protocol'] = "http/1.1";
        $swoole->server['server_port'] = "8080";
        $swoole->header['host'] = "localhost";
        $swoole->header['foo'] = "bar";
        $request = RequestFactory::create(
            $swoole
        );

        $this->assertEquals(true, $request instanceof ServerRequestInterface);
    }

    public function testShouldReturnRequestInterfaceWhenReactRequestGivenToCreate()
    {
        $request = RequestFactory::create(
            new ReactReq(
                'GET',
                'https://localhost'
            )
        );

        $this->assertEquals(true, $request instanceof ServerRequestInterface);
        $this->assertEquals(true, $request instanceof ReactReq);
    }
}
