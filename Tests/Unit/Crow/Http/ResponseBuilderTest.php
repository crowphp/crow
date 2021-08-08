<?php

declare(strict_types=1);

namespace Tests\Unit\Crow\Http;

use Crow\Http\ResponseBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use TypeError;

class ResponseBuilderTest extends TestCase
{

    public function testMakeResponseWithCodeAndBody()
    {

        $this->assertInstanceOf(
            ResponseInterface::class,
            ResponseBuilder::makeResponseWithCodeAndBody(405, "Here")
        );
    }

    public function testInvalidCodeTypeWhenMakingResponseWithCode()
    {
        $this->expectException(TypeError::class);
        ResponseBuilder::makeResponseWithCodeAndBody("10aa0", "Here");
    }
}
