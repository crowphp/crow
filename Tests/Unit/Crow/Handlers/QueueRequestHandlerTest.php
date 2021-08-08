<?php

namespace Tests\Unit\Crow\Handlers;

use Crow\Http\Response;
use PHPUnit\Framework\TestCase;
use Crow\Handlers\QueueRequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class QueueRequestHandlerTest extends TestCase
{

    public function testHandle()
    {
        $handler = new QueueRequestHandler();
        $handler->add(function (
            ServerRequestInterface $request,
            RequestHandlerInterface $handler
): ResponseInterface {
            return new Response();
        });
        $response = $handler->handle(
            ServerRequestFactory::fromGlobals(
                $_SERVER,
                $_GET,
                $_POST,
                $_COOKIE,
                $_FILES
            )
        );
        $this->assertEquals(true, $response instanceof ResponseInterface);
    }

    public function testAdd()
    {
        $handler = new QueueRequestHandler();
        $handler->add(
            new class implements MiddlewareInterface {
                public function process(
                    ServerRequestInterface $request,
                    RequestHandlerInterface $handler
                ): ResponseInterface {
                    return $handler->handle($request);
                }
            }
        );
        $handler->add(
            new class implements MiddlewareInterface {
                public function process(
                    ServerRequestInterface $request,
                    RequestHandlerInterface $handler
                ): ResponseInterface {
                    return new Response();
                }
            }
        );
        $response = $handler->handle(
            ServerRequestFactory::fromGlobals(
                $_SERVER,
                $_GET,
                $_POST,
                $_COOKIE,
                $_FILES
            )
        );
        $this->assertEquals(true, $response instanceof ResponseInterface);
    }
}
