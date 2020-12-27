<?php
declare(strict_types=1);

namespace Crow\Http\Server;

use Crow\Http\QueueRequestHandler;
use Crow\Http\Server\Exceptions\InvalidServerType;

class Factory
{
    public const REACT_SERVER = 1;
    public const SWOOLE_SERVER = 2;

    public static function create(int $serverType): ServerInterface
    {
        $requestHandler = new QueueRequestHandler();
        switch ($serverType) {
            case self::REACT_SERVER;
                return new ReactServer($requestHandler);
            case self::SWOOLE_SERVER;
                return new SwooleServer($requestHandler);
        }

        throw new InvalidServerType("Invalid server type provided");
    }

}