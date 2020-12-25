<?php
declare(strict_types=1);

namespace Tests\Unit\Crow;

use Crow\ErrorHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;


class ErrorHandlerTest extends TestCase
{

    public function testExceptionToBody()
    {

        $this->assertIsString(
            ErrorHandler::exceptionToBody(new \LogicException('Test Exception'))
        );
    }


}
