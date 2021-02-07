<?php

declare(strict_types=1);

namespace Crow\Handlers;

use Crow\Http\RequestFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReactRequestHandler extends CrowRequestHandler
{
    private QueueRequestHandlerBuilder $queueRequestHandlerBuilder;
    private RequestFactory $requestFactory;

    public function __construct(
        QueueRequestHandlerBuilder $queueRequestHandlerBuilder,
        RequestFactory $requestFactory
    ) {
        $this->queueRequestHandlerBuilder = $queueRequestHandlerBuilder;
        $this->requestFactory = $requestFactory;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->queueRequestHandlerBuilder->build(
            $this->middlewaresList,
            $this->router
        )->handle(
            $this->requestFactory->create($request)
        );
    }
}
