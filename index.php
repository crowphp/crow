#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Crow\Server;
use Crow\Router\RouterInterface;

$app = new Server;
$router = Crow\Router\Factory::make();

$router->get('/', function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
    $response->getBody()->write('Hello World');
    return $response->withHeader('Content-Type', 'text');;
});

$router->get('/id/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $id): ResponseInterface {
    $response->getBody()->write('Hello World' . $id);
    return $response;
});

$router->addGroup('/yousaf', function (RouterInterface $router) {
    $router->get('/sunny/id/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $id): ResponseInterface {
        $response->getBody()->write('Yousaf ' . $id);
        return $response->withHeader('Content-Type', 'text/plain');;
    });

    $router->get('/mani/id/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $id): ResponseInterface {
        $response->getBody()->write('Mani ' . $id);
        throw new Exception('Hey i am an exception');
    });
});
$app->withRouter($router);

//Uncaught Exceptions
$app->on('error', function ($error) {
    var_dump($error->getMessage());
});

$app->listen(5005);
