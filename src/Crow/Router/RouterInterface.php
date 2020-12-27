<?php
declare(strict_types=1);

namespace Crow\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

interface RouterInterface
{

    public function addRoute(array|string $httpMethod, string $route, mixed $handler);

    public function addGroup(string $prefix, callable $callback);

    public function get(string $route, mixed $handler);

    public function post(string $route, mixed $handler);

    public function put(string $route, mixed $handler);

    public function delete(string $route, mixed $handler);

    public function patch(string $route, mixed $handler);

    public function head(string $route, mixed $handler);

    public function dispatch(RequestInterface $request): ResponseInterface;

}