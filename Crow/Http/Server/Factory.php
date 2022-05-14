<?php

declare(strict_types=1);

namespace Crow\Http\Server;

use Crow\Handlers\QueueRequestHandlerBuilder;
use Crow\Handlers\SwooleRequestHandler;
use Crow\Http\PsrToSwooleResponseBuilder;
use Crow\Http\RequestFactory;
use Crow\Http\Server\Exceptions\InvalidServerType;
use Crow\Middlewares\RoutersList;
use Crow\Middlewares\UserMiddlewaresList;

class Factory
{
    public const REACT_SERVER = 1;
    public const SWOOLE_SERVER = 2;

    public static function create(int $serverType): ServerInterface
    {
        switch ($serverType) {
            case self::SWOOLE_SERVER:
                return new CrowSwooleServer(
                    new SwoolePHPServer(),
                    new SwooleRequestHandler(
                        new QueueRequestHandlerBuilder(),
                        new PsrToSwooleResponseBuilder(),
                        new RequestFactory()
                    ),
                    new UserMiddlewaresList(),
                    new RoutersList()
                );
        }
        throw new InvalidServerType("Invalid server type provided");
    }
}
