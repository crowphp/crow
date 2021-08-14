<?php

declare(strict_types=1);

namespace Crow\Router\Types;

class RoutePath
{
    private ?string $prefix;
    private string $routePath;

    public function __construct(string $routePath, ?string $prefix = null)
    {
        $this->prefix = $prefix;
        $this->routePath = $routePath;
    }

    public function getFullRoutePath(): string
    {
        return ($this->prefix) ? $this->prefix . $this->routePath : $this->routePath;
    }
}
