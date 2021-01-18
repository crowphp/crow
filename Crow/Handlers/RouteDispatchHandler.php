<?php declare(strict_types=1);

namespace Crow\Handlers;

use Crow\Middlewares\FinalMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RouteDispatchHandler
{
    private QueueRequestHandler $requestHandler;

    public function __construct(QueueRequestHandler $requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    public function __invoke(array $middlewares,
                             callable $handler,
                             ServerRequestInterface $request,
                             array $args): ResponseInterface
    {
        if (count($middlewares) > 0) {
            foreach ($middlewares as $middleware) {
                $this->requestHandler->add($middleware);
            }
        }
        $this->requestHandler->add(new FinalMiddleware($handler, $args));
        return $this->requestHandler->handle($request);
    }
}