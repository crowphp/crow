<?php

declare(strict_types=1);

namespace Crow\Middlewares;

use Crow\Http\DefaultHeaders;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use React\Http\Message\Response;

class FinalMiddleware implements MiddlewareInterface
{
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
            new Response(
                200,
                array_merge(
                    DefaultHeaders::get(),
                    [
                        "Host" => $request->getHeader("host")
                    ]
                )
            ),
            ...array_values($this->args)
        );
    }
}
