<?php declare(strict_types=1);

namespace Crow\Handlers;

use Crow\Http\PsrToSwooleResponseBuilder;
use Crow\Http\RequestFactory;
use Crow\Middlewares\ErrorMiddleware;
use Crow\Middlewares\RoutingMiddleware;
use Crow\Middlewares\UserMiddlewaresList;
use Crow\Router\RouterInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;

class SwooleRequestHandler implements CrowRequestHandler
{

    private RouterInterface $router;
    private UserMiddlewaresList $middlewaresList;

    function __construct(
        private QueueRequestHandler $queueRequestHandler,
        private PsrToSwooleResponseBuilder $psrToSwooleResponseBuilder,
        private RequestFactory $requestFactory
    )
    {
    }

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function setMiddlewaresList(UserMiddlewaresList $middlewaresList)
    {
        $this->middlewaresList = $middlewaresList;
    }

    public function __invoke(Request $request, Response $response)
    {

        $this->psrToSwooleResponseBuilder->toSwoole(
            $this->makeMiddlewareHandlerForRequest()->handle(
                $this->requestFactory->create($request)
            ),
            $response
        )->end();
    }

    private function makeMiddlewareHandlerForRequest(): QueueRequestHandler
    {
        $this->queueRequestHandler->add(new ErrorMiddleware());
        foreach ($this->middlewaresList->getMiddlewares() as $middleware) {
            $this->queueRequestHandler->add($middleware);
        }
        $this->queueRequestHandler->add(new RoutingMiddleware($this->router));
        return $this->queueRequestHandler;
    }
}