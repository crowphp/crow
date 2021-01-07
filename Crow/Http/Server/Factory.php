<?php declare(strict_types=1);

namespace Crow\Http\Server;

use Crow\Handlers\QueueRequestHandler;
use Crow\Handlers\SwooleRequestHandler;
use Crow\Http\PsrToSwooleResponseBuilder;
use Crow\Http\RequestFactory;
use Crow\Http\Server\Exceptions\InvalidServerType;
use Crow\Http\SwooleRequest;
use Crow\Middlewares\UserMiddlewaresList;


class Factory
{
    public const REACT_SERVER = 1;
    public const SWOOLE_SERVER = 2;

    public static function create(int $serverType): ServerInterface
    {
        switch ($serverType) {
            case self::REACT_SERVER;
                return new CrowReactServer(new ReactPHPServer());
            case self::SWOOLE_SERVER;
                return new CrowSwooleServer(
                    new SwoolePHPServer(),
                    new SwooleRequestHandler(
                        new QueueRequestHandler(),
                        new PsrToSwooleResponseBuilder(),
                        new RequestFactory()
                    ),
                    new UserMiddlewaresList()
                );
        }
        throw new InvalidServerType("Invalid server type provided");
    }

}