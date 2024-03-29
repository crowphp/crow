#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Crow\Http\Server\Factory as CrowServer;
use Crow\Router\RouterInterface;
use Psr\Http\Server\RequestHandlerInterface;

$app = CrowServer::create(CrowServer::SWOOLE_SERVER);
$router = Crow\Router\Factory::make();

$router->get('/', function (RequestInterface $request, ResponseInterface $response) {
    $key = 'crow\php';
    if (isset($request->getCookieParams()[$key])) {
        $response->getBody()->write("Your cookie value is: " . $request->getCookieParams()[$key]);
        return $response;
    }
    $response->getBody()->write('Hello World home' . $request->getProtocolVersion());
    return $response->withHeader(
        'Set-Cookie',
        urlencode($key) . '=' . urlencode('test;more')
    );
});

$router->get('/sleep5', function (RequestInterface $request, ResponseInterface $response) {
    sleep(5);
    $response->getBody()->write('Hello World after 5 seconds');
    return $response;
});

$router->post('/file', function (RequestInterface $request, ResponseInterface $response) {
    $files = $request->getUploadedFiles();
    rename($files["screenshot_png"]["tmp_name"], "/tmp/" . $files["screenshot_png"]["name"]);
    $response->getBody()->write('File Uploaded ' . $files["screenshot_png"]["name"]);
    return $response;
});
$router->get('/id/{id}', function (RequestInterface $request, ResponseInterface $response, $id): ResponseInterface {
    $response->getBody()->write('Hello World' . $id);
    return $response;
})->middleware(function (RequestInterface $request, RequestHandlerInterface $next) {
    echo "This is a local middleware 1\n";
    return $next->handle($request);
})->middleware(function (RequestInterface $request, RequestHandlerInterface $next) {
    echo "This is a local middleware 2\n";
    return $next->handle($request);
});


$router->addGroup('/yousaf', function (RouterInterface $router) {
    $router->get('/sunny/id/{id}', function (RequestInterface $request, ResponseInterface $response, $id): ResponseInterface {

        $response->getBody()->write(json_encode(["message" => $id]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    })->middleware(function (RequestInterface $request, RequestHandlerInterface $next) {
        echo "This is a local middleware 1 for sunny \n";
        return $next->handle($request);
    });

    $router->get('/mani/id/{id}', function (RequestInterface $request, ResponseInterface $response, $id): ResponseInterface {
        $response->getBody()->write('Mani ' . $id);
        throw new Exception('Hey i am an exception');
    });
    $router->addGroup("/ehsan", function (RouterInterface $router) {
        $router->get('/naqvi/id/{id}', function (RequestInterface $request, ResponseInterface $response, $id): ResponseInterface {
            $response->getBody()->write('naqvi ' . $id);
            return $response;
        });
    });
}, function (RequestInterface $request, RequestHandlerInterface $next) {
    echo "This is a group middleware 1\n";
    return $next->handle($request);
}, function (RequestInterface $request, RequestHandlerInterface $next) {
    echo "This is a group middleware 2\n";
    return $next->handle($request);
});

$app->withRouter($router);

$app->withTimeout(5);


$app->use(function (RequestInterface $request, RequestHandlerInterface $next) {
    echo "This is a global middleware 1\n";
    return $next->handle($request);
});

$app->use(function (RequestInterface $request, RequestHandlerInterface $next) {
    echo "This is a global middleware 2\n";
    return $next->handle($request);
});

//Uncaught Exceptions
$app->on('workererror', function ($error) {
    var_dump($error->getMessage());
});

$app->on('start', function ($server) {
    echo "CrowPHP server is listening on port $server->host:$server->port " . PHP_EOL;
});

$app->configs(['reactor_num' => 2,
    'worker_num' => 4,
    'backlog' => 128,
    'max_request' => 50,
    'dispatch_mode' => 1,]);

$app->listen(5005, "0.0.0.0");
