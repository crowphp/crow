<?php

namespace Test\Unit\Crow\Router;

use FastRoute;
use Crow\Router\FastRouteDispatcher;
use Crow\Router\FastRouter;
use PHPUnit\Framework\TestCase;

class FastRouteDispatcherTest extends TestCase
{

    public function testMake()
    {
        $fastRouteDispatcher = new FastRouteDispatcher();
        $dispatcher = $fastRouteDispatcher->make([
            [
                FastRouter::HANDLER_LABEL => "",
                FastRouter::HTTP_METHOD_LABEL => "",
                FastRouter::ROUTE_LABEL => ""
            ]
        ]);

        $this->assertTrue($dispatcher instanceof FastRoute\Dispatcher);
    }
}
