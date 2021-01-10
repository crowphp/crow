Fast, unopinionated, minimalist web framework for PHP.

![Build Status](https://github.com/crowphp/crow/workflows/build/badge.svg)
![License](https://img.shields.io/github/license/crowphp/crow)
![Coverage](https://img.shields.io/endpoint?url=https://badger.crowphp.com/coverage/master)

### Installation

#### Requirements

1. PHP >8.0
2. Swoole PHP extension

```
$ pecl install swoole
```

Run the following command in a new PHP project:

```
composer install crowphp/crow
```

### Usage

```php
<?php
require 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Crow\Http\Server\Factory as CrowServer;

$app = CrowServer::create(CrowServer::SWOOLE_SERVER);
$router = Crow\Router\Factory::make();

$router->get('/', function (RequestInterface $request, ResponseInterface $response) {
    $response->getBody()->write('Hello World');
    return $response;
});

$app->withRouter($router);

$app->listen(5005);
```
