<?php declare(strict_types=1);

namespace Test\Unit\Crow\Http;

use Crow\Http\RequestFactory;
use Crow\Http\SwooleRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request as SwooleReq;
use React\Http\Message\ServerRequest as ReactReq;

class RequestFactoryTest extends TestCase
{

    public function testShouldReturnRequestInterfaceWhenSwooleRequestGivenToCreate()
    {
        $request = RequestFactory::create(
            new SwooleReq()
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
