<?php
declare(strict_types=1);

namespace Crow;

class DefaultHeaders
{
    public static function get(): array
    {
        return [
            "Server" => "CrowPHP/1",
        ];
    }
}