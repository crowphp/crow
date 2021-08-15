<?php

declare(strict_types=1);

namespace Crow\Middlewares;

use Crow\Http\Response;
use Crow\Http\DefaultHeaders;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FinalMiddleware implements MiddlewareInterface
{
    private const HOST_HEADER_LABEL = "host";
    private $handler;
    /**
     * @var string[]
     */
    private array $args;

    /**
     * FinalMiddleware constructor.
     * @param callable $handler
     * @param string[] $args
     */
    public function __construct(callable $handler, array $args)
    {
        $this->handler = $handler;
        $this->args = $args;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return call_user_func(
            $this->handler,
            $request,
            $this->makeResponse($request),
            ...array_values($this->args)
        );
    }

    private function makeResponse(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $defaultHeaders = DefaultHeaders::get();
        if ($request->hasHeader(self::HOST_HEADER_LABEL)) {
            $defaultHeaders[self::HOST_HEADER_LABEL] = $request->getHeaderLine(self::HOST_HEADER_LABEL);
        }

        foreach ($defaultHeaders as $defaultHeader => $defaultHeaderValue) {
            $response = $response->withHeader(
                $defaultHeader,
                $defaultHeaderValue
            );
        }

        return $response;
    }
}
