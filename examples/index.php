#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Crow\Server;
use Crow\Router\RouterInterface;


$app = new Server;
$router = Crow\Router\Factory::make();

$router->get('/', function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {

    $promise = new React\Promise\Promise(function ($resolve, $reject) use ($app) {
        $app->getLoop()->addTimer(10, function () use ($resolve) {
            $resolve();
        });
    });
    return $promise->then(function () use ($response) {
        $response->getBody()->write('After 10 seconds');
        return $response
            ->withHeader('Content-Type', 'html')
            ->withHeader('Set-Cookie', urlencode('username') . '=' . urlencode('test'));
    });

});

$router->get('/id/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $id): ResponseInterface {
    $response->getBody()->write('Hello World' . $id);
    return $response;
});

$router->addGroup('/yousaf', function (RouterInterface $router) {
    $router->get('/sunny/id/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $id): ResponseInterface {
        $response->getBody()->write(json_encode(["message" => $id]));
        return $response->withHeader('Content-Type', 'application/json');;
    });

    $router->get('/mani/id/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $id): ResponseInterface {
        $response->getBody()->write('Mani ' . $id);
        throw new Exception('Hey i am an exception');
    });
});
$app->withRouter($router);

//$app->withTimeout(20);
//Uncaught Exceptions

$app->use(function ($request, $next) {
    echo "This is a global middleware\n";
    return $next($request);
});

$app->on('error', function ($error) {
    var_dump($error->getMessage());
});

$app->listen(5005);
