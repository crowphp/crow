<?php

declare(strict_types=1);

namespace Crow\Http\Server;

use Swoole;

class SwoolePHPServer
{
    public function getServer(int $port, string $host,): Swoole\Http\Server
    {
        return new Swoole\Http\Server($host, $port);
    }
}
