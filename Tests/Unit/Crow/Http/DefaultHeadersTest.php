<?php declare(strict_types=1);

namespace Tests\Unit\Crow\Http;

use Crow\Http\DefaultHeaders;
use PHPUnit\Framework\TestCase;

class DefaultHeadersTest extends TestCase
{

    public function testIfHasServerKey()
    {
        $this->assertArrayHasKey('Server',
            DefaultHeaders::get()
        );

        $this->assertEquals('CrowPHP/1',
            DefaultHeaders::get()["Server"]
        );
    }
}
