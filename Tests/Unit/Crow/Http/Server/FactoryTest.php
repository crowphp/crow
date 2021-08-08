<?php

namespace Tests\Unit\Crow\Http\Server;

use Crow\Http\Server\Exceptions\InvalidServerType;
use Crow\Http\Server\Factory;
use Crow\Http\Server\CrowReactServer;
use Crow\Http\Server\CrowSwooleServer;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{

    public function testCreateSwooleServer()
    {
        $server = Factory::create(Factory::SWOOLE_SERVER);

        $this->assertTrue($server instanceof CrowSwooleServer);
    }

    public function testCreateInvalidServerException()
    {
        $this->expectException(InvalidServerType::class);
        Factory::create(3);
    }
}
