#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Crow\Http\Server\Factory as CrowServer;
use Crow\Router\RouterInterface;


$app = CrowServer::create(CrowServer::SWOOLE_SERVER);
$router = Crow\Router\Factory::make();

$router->get('/', function (RequestInterface $request, ResponseInterface $response) {
    sleep(20);
    $response->getBody()->write('Hello World home');
    return $response;
});

$router->get('/id/{id}', function (RequestInterface $request, ResponseInterface $response, $id): ResponseInterface {
    $response->getBody()->write('Hello World' . $id);
    return $response;
});

$router->addGroup('/yousaf', function (RouterInterface $router) {
    $router->get('/sunny/id/{id}', function (RequestInterface $request, ResponseInterface $response, $id): ResponseInterface {

        $response->getBody()->write(json_encode(["message" => $id]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    });

    $router->get('/mani/id/{id}', function (RequestInterface $request, ResponseInterface $response, $id): ResponseInterface {
        $response->getBody()->write('Mani ' . $id);
        throw new Exception('Hey i am an exception');
    });
});
$app->withRouter($router);

//$app->withTimeout(5);
//Uncaught Exceptions

$app->use(function (RequestInterface $request, $next) {
    echo "This is a global middleware 1\n";
    return $next->handle($request);
});

$app->use(function (RequestInterface $request, $next) {
    echo "This is a global middleware 2\n";
    return $next->handle($request);
});

$app->on('workererror', function ($error) {
    var_dump($error->getMessage());
});

$app->on('start', function ($server) {
    echo "CrowPHP server is listening on port $server->host:$server->port " . PHP_EOL;
});

$app->listen(5005, "0.0.0.0");
