<?php

namespace Test\Unit\Crow\Middlewares;

use Crow\Handlers\QueueRequestHandler;
use Crow\Middlewares\ErrorMiddleware;
use Exception;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;

class ErrorMiddlewareTest extends TestCase
{

    use ProphecyTrait;

    public function testProcess()
    {
        $requestHandler = new QueueRequestHandler();
        $requestHandler->add(
            function ($request, $handler) {
                throw new Exception("I am an error");
            }
        );
        $request = ServerRequestFactory::fromGlobals(
            $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
        );
        $errorMiddleware = new ErrorMiddleware();

        $response = $errorMiddleware->process(
            $request,
            $requestHandler
        );


        $this->assertEquals(true, $response instanceof ResponseInterface);

    }

}
