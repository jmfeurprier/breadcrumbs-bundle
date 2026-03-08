<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Breadcrumbs;

readonly class Breadcrumb
{
    /**
     * @param array<string, mixed> $routeParameters
     */
    public function __construct(
        private string $label,
        private string $routeName,
        private array $routeParameters,
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRouteParameters(): array
    {
        return $this->routeParameters;
    }
}
