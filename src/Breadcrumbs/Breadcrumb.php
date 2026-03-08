<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Breadcrumbs;

readonly class Breadcrumb
{
    public function __construct(
        private string $label,
        private string $path,
        private string $routeName,
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }
}
