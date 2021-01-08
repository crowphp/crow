<?php declare(strict_types=1);

namespace Crow\Http\Server;

use Crow\Http\RequestFactory;
use Crow\Handlers\ReactRequestHandler;
use Crow\Handlers\SwooleRequestHandler;
use Crow\Http\PsrToSwooleResponseBuilder;
use Crow\Middlewares\UserMiddlewaresList;
use Crow\Handlers\QueueRequestHandlerBuilder;
use Crow\Http\Server\Exceptions\InvalidServerType;

class Factory
{
    public const REACT_SERVER = 1;
    public const SWOOLE_SERVER = 2;

    public static function create(int $serverType): ServerInterface
    {
        switch ($serverType) {
            case self::REACT_SERVER;
                return new CrowReactServer(
                    new ReactPHPServer(),
                    new ReactRequestHandler(
                        new QueueRequestHandlerBuilder(),
                        new RequestFactory()
                    ),
                    new UserMiddlewaresList()
                );
            case self::SWOOLE_SERVER;
                return new CrowSwooleServer(
                    new SwoolePHPServer(),
                    new SwooleRequestHandler(
                        new QueueRequestHandlerBuilder(),
                        new PsrToSwooleResponseBuilder(),
                        new RequestFactory()
                    ),
                    new UserMiddlewaresList()
                );
        }
        throw new InvalidServerType("Invalid server type provided");
    }

}