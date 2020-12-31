<?php declare(strict_types=1);

namespace Test\Unit\Crow\Router;

use Crow\Router\Factory;
use Crow\Router\RouterInterface;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{

    public function testMakeReturnsRouterInterface()
    {
        $router = Factory::make();
        $this->assertEquals(true, $router instanceof RouterInterface);
        $this->assertEquals(true, method_exists($router, 'get'));
        $this->assertEquals(true, method_exists($router, 'post'));
        $this->assertEquals(true, method_exists($router, 'put'));
        $this->assertEquals(true, method_exists($router, 'delete'));
        $this->assertEquals(true, method_exists($router, 'patch'));
        $this->assertEquals(true, method_exists($router, 'head'));
        $this->assertEquals(true, method_exists($router, 'dispatch'));
        $this->assertEquals(true, method_exists($router, 'addRoute'));
        $this->assertEquals(true, method_exists($router, 'addGroup'));
    }
}
