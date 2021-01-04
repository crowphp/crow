<?php

namespace Tests\Unit\Crow\Handlers;

use Crow\Handlers\QueueRequestHandler;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use React\Http\Message\Response;

class QueueRequestHandlerTest extends TestCase
{

    public function testHandle()
    {
        $handler = new QueueRequestHandler();
        $handler->add(function (ServerRequestInterface $request,
                                RequestHandlerInterface $handler): ResponseInterface {
            return new Response(200);
        });
        $response = $handler->handle(
            ServerRequestFactory::fromGlobals(
                $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
            )
        );
        $this->assertEquals(true, $response instanceof ResponseInterface);
    }

    public function testAdd()
    {
        $handler = new QueueRequestHandler();
        $handler->add(
            new class implements MiddlewareInterface {
                public function process(ServerRequestInterface $request,
                                        RequestHandlerInterface $handler): ResponseInterface
                {
                    return $handler->handle($request);
                }
            });
        $handler->add(
            new class implements MiddlewareInterface {
                public function process(ServerRequestInterface $request,
                                        RequestHandlerInterface $handler): ResponseInterface
                {
                    return new Response(200);
                }
            });
        $response = $handler->handle(
            ServerRequestFactory::fromGlobals(
                $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
            )
        );
        $this->assertEquals(true, $response instanceof ResponseInterface);
    }
}
