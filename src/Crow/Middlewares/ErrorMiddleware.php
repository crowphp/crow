<?php


namespace Crow\Middlewares;


use Crow\Handlers\ErrorHandler;
use Crow\Http\ResponseBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ErrorMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Exception | \LogicException $exception) {
            return ResponseBuilder::makeResponseWithCodeAndBody(
                500,
                ErrorHandler::exceptionToBody($exception));
        }
    }
}