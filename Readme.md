Fast un-opinionated minimalist web framework and server for PHP built on top of Async PHP servers  (SwoolePHP and ReactPHP).
CrowPHP lets you build real microservices in PHP without the use of PHP-FPM/Nginx or Apache.


![Build Status](https://github.com/crowphp/crow/workflows/build/badge.svg)
![License](https://img.shields.io/github/license/crowphp/crow)
![Coverage](https://img.shields.io/endpoint?url=https://badger.crowphp.com/coverage/0.x)

### Installation

#### Requirements

1. PHP >8.0
2. Swoole PHP extension

```
$ pecl install swoole
```

Installation of CrowPHP via composer, the following command will install the framework and all of its dependencies with it.

```
composer install crowphp/crow
```

### Hello world microservice using CrowPHP

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
You may quickly test your newly built service as follows:
```bash
$ php index.php
```
Going to http://localhost:5005 will now display "Hello World".

For more information on how to configure your web server, see the Documentation.

## Tests
To execute the test suite, you'll need to install all development dependencies.
```
$ git clone https://github.com/crowphp/crow
$ composer install
$ composer test
```
## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Learn More
Learn more at these links:
- [Website](https://www.crowphp.com)

## Security
If you discover security related issues, please email yousaf@bmail.pk or use the issue tracker.

## License

The Crow Framework is licensed under the MIT license. See [License File](LICENSE.md) for more information.

