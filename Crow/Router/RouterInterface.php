<?php declare(strict_types=1);

namespace Crow\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{

    public function addRoute(string $httpMethod, string $route, callable $handler);

    public function addGroup(string $prefix, callable $callback, mixed ...$handlers);

    public function get(string $route, callable $handler);

    public function post(string $route, callable $handler);

    public function put(string $route, callable $handler);

    public function delete(string $route, callable $handler);

    public function patch(string $route, callable $handler);

    public function head(string $route, callable $handler);

    public function dispatch(ServerRequestInterface $request): ResponseInterface;

}