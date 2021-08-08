<?php

declare(strict_types=1);

namespace Test\Unit\Crow\Http;

use Crow\Http\Request;
use Crow\Http\RequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request as SwooleReq;

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
        $requestFactory = new RequestFactory();
        $request = $requestFactory->create($swoole);

        $this->assertEquals(true, $request instanceof ServerRequestInterface);
    }

    public function testShouldReturnRequestInterfaceWhenReactRequestGivenToCreate()
    {
        $factory = new RequestFactory();
        $request = $factory->create(
            Request::makeRequest(
                'GET',
                'https://localhost'
            )
        );

        $this->assertEquals(true, $request instanceof ServerRequestInterface);
    }
}
