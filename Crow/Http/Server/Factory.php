<?php declare(strict_types=1);

namespace Crow\Http\Server;

use Crow\Http\Server\Exceptions\InvalidServerType;

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
                return new CrowSwooleServer;
        }
        throw new InvalidServerType("Invalid server type provided");
    }

}