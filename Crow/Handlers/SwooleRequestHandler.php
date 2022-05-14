<?php

declare(strict_types=1);

namespace Crow\Handlers;

use Swoole\Http\Request;
use Swoole\Http\Response;
use Crow\Http\RequestFactory;
use Crow\Http\PsrToSwooleResponseBuilder;

class SwooleRequestHandler extends CrowRequestHandler
{
    private QueueRequestHandlerBuilder $queueRequestHandlerBuilder;
    private PsrToSwooleResponseBuilder $psrToSwooleResponseBuilder;
    private RequestFactory $requestFactory;

    public function __construct(
        QueueRequestHandlerBuilder $queueRequestHandlerBuilder,
        PsrToSwooleResponseBuilder $psrToSwooleResponseBuilder,
        RequestFactory $requestFactory
    ) {
        $this->queueRequestHandlerBuilder = $queueRequestHandlerBuilder;
        $this->psrToSwooleResponseBuilder = $psrToSwooleResponseBuilder;
        $this->requestFactory = $requestFactory;
    }

    public function __invoke(Request $request, Response $response): void
    {

        $this->psrToSwooleResponseBuilder->toSwoole(
            $this->queueRequestHandlerBuilder->build(
                $this->middlewaresList,
                $this->routers
            )->handle(
                $this->requestFactory->create($request)
            ),
            $response
        )->end();
    }
}
