<?php
declare(strict_types=1);

namespace Crow\Http\Server;


use Crow\Router\RouterInterface;
use Crow\Server\Exceptions\InvalidEventType;
use Psr\Http\Message\RequestInterface;
use Swoole\Http\Request;
use Swoole\Http\Server;
use Swoole\Http\Response;
use Crow\Http\RequestFactory;
use Crow\Http\PsrToSwooleResponseBuilder;
/**
 * TODO: Add functionality to handle events
 * TODO: Add functionality to provide global middleware handler
 */
class SwooleServer implements ServerInterface
{

    private RouterInterface $router;
    private array $eventListeners = [];
    private array $invalidEvents = ['request'];
    private int $loopTimeoutSeconds = 0;
    private array $middlewares = array();

    public function listen(int $port = 5000, string $host = "127.0.0.1")
    {

        $http = new Server($host, $port);
        $app = $this;
        $http->on('request', function (Request $request, Response $response) use ($app) {
            $responseMerger = new PsrToSwooleResponseBuilder();
            $responseMerger->toSwoole(
                $app->handle(RequestFactory::create($request)),
                $response
            )->end();
        });

        $http->start();
    }

    private function handle(RequestInterface $request)
    {
        return $this->router->dispatch($request);
    }

    public function withTimeout(int $seconds)
    {
        // TODO: Implement withTimeout() method.
    }

    public function on(string $event, callable $callback)
    {
        if (in_array($event, $this->invalidEvents)) {
            throw new InvalidEventType("This event type is not permitted");
        }
        array_push($this->eventListeners, $event);
    }

    public function withRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function use(callable $middleware)
    {
        array_push($this->middlewares, $middleware);
    }
}