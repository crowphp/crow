<?php

declare(strict_types=1);

namespace Crow\Router\Types;

use MyCLabs\Enum\Enum;

final class RouteMethod extends Enum
{

    public const GET = 'GET';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';
    public const PATCH = 'PATCH';
    public const HEAD = 'HEAD';
}
