<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

readonly class Breadcrumb
{
    public function __construct(
        private string $label,
        private string $path
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
}
