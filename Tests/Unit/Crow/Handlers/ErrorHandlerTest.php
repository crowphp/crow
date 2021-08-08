<?php

declare(strict_types=1);

namespace Tests\Unit\Crow\Handlers;

use Crow\Handlers\ErrorHandler;
use PHPUnit\Framework\TestCase;

class ErrorHandlerTest extends TestCase
{

    public function testExceptionToBody()
    {

        $this->assertIsString(
            ErrorHandler::exceptionToBody(new \LogicException('Test Exception'))
        );
    }
}
