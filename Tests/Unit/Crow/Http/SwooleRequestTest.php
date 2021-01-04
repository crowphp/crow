<?php declare(strict_types=1);

namespace Test\Unit\Crow\Http;

use Crow\Http\SwooleRequest;
use Laminas\Diactoros\Stream;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Swoole\Http\Request as SwooleRawReq;

class SwooleRequestTest extends TestCase
{

    private function makeRequest(): RequestInterface
    {
        $swoole = new SwooleRawReq();
        $swoole->server['query_string'] = "foo=bar";
        $swoole->server['request_uri'] = "/uri";
        $swoole->server['request_method'] = "GET";
        $swoole->server['server_protocol'] = "http/1.1";
        $swoole->server['server_port'] = "8080";
        $swoole->header['host'] = "localhost";
        $swoole->header['foo'] = "bar";
        return new SwooleRequest(
            $swoole,
            new Psr17Factory(),
            new Psr17Factory()
        );
    }

    private function makeUriFactory(): UriFactoryInterface
    {
        return new Psr17Factory();
    }

    public function testGetRequestTarget()
    {

        $this->assertEquals(
            "/uri?foo=bar",
            $this->makeRequest()->getRequestTarget()
        );
    }

    public function testGetProtocolVersion()
    {

        $this->assertEquals(
            "http/1.1",
            $this->makeRequest()->getProtocolVersion()
        );
    }

    public function testHasHeader()
    {

        $this->assertEquals(
            true,
            $this->makeRequest()->hasHeader('foo'));
    }

    public function testWithAddedHeader()
    {
        $request = $this->makeRequest()->withAddedHeader('foo2', "bar2");
        $this->assertEquals(
            true,
            $request->hasHeader("foo2"));
        $this->assertEquals(
            "bar2",
            $request->getHeaderLine("foo2"));
    }


    public function testWithAddedHeaderOnExisting()
    {
        $request = $this->makeRequest()->withAddedHeader('foo', "bar2");
        $this->assertEquals(
            true,
            $request->hasHeader("foo"));
        $this->assertEquals(
            "bar,bar2",
            $request->getHeaderLine("foo"));
    }

    public function testWithRequestTarget()
    {
        $request = $this->makeRequest()->withRequestTarget("/foo");

        $this->assertEquals(
            "/foo",
            $request->getRequestTarget());
    }

    public function testWithoutHeader()
    {
        $request = $this->makeRequest()->withoutHeader("foo");

        $this->assertEquals(
            false,
            $request->hasHeader("foo"));
    }

    public function testWithoutHeaderWhenItsAlreadyNotPresent()
    {
        $request = $this->makeRequest()->withoutHeader("foo5");

        $this->assertEquals(
            false,
            $request->hasHeader("foo5"));
    }

    public function testWithBody()
    {
        $stream = new Stream("php://temp", "rw");
        $stream->write("Hello");
        $request = $this->makeRequest()->withBody($stream);
        $this->assertEquals(
            "Hello",
            $request->getBody()->__toString());
    }

    public function testGetBody()
    {
        $stream = new Stream("php://temp", "rw");
        $stream->write("Hello");
        $request = $this->makeRequest()->withBody($stream);
        $this->assertEquals(
            "Hello",
            $request->getBody()->__toString());
        $this->assertEquals(
            true,
            $request->getBody() instanceof StreamInterface);
    }

    public function testGetHeader()
    {
        $this->assertEquals(
            ["bar"],
            $this->makeRequest()->getHeader("foo"));
    }

    public function testWithProtocolVersion()
    {
        $this->assertEquals(
            "2.0",
            $this->makeRequest()->withProtocolVersion("2.0")->getProtocolVersion());
    }

    public function testWithHeader()
    {
        $this->assertEquals(
            ["bar3"],
            $this->makeRequest()->withHeader("foo3", "bar3")->getHeader("foo3"));
    }

    public function testGetHeaderLine()
    {
        $this->assertEquals(
            "bar",
            $this->makeRequest()->getHeaderLine("foo"));
    }

    public function testGetMethod()
    {
        $this->assertEquals(
            "GET",
            $this->makeRequest()->getMethod());
    }

    public function testWithUri()
    {
        $uri = $this->makeUriFactory()->createUri("localhost/uri?foo=bar");
        $this->assertEquals(
            $uri,
            $this->makeRequest()->withUri($uri)->getUri());
    }

    public function testGetHeaders()
    {
        $this->assertEquals(
            ["foo" => "bar", "host" => "localhost"],
            $this->makeRequest()->getHeaders());
    }

    public function testWithAuthHeaders()
    {
        $this->assertEquals(
            [
                "foo" => "bar",
                "host" => "localhost",
                "authorization" => "Basic 12"
            ],
            $this->makeRequest()
                ->withHeader("authorization", "Basic 12")
                ->getHeaders()
        );
    }


    public function testWithMethod()
    {
        $this->assertEquals(
            "POST",
            $this->makeRequest()->withMethod("POST")->getMethod());
    }

    public function testGetUri()
    {
        $this->assertEquals(
            $this->makeUriFactory()->createUri("localhost/uri?foo=bar"),
            $this->makeRequest()->getUri());
    }
}
