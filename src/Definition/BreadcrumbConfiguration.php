<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Definition;

readonly class BreadcrumbConfiguration
{
    public function __construct(
        private string $routeName,
        private string $label,
        private StringMap $parameters,
        private ?ParentBreadcrumbConfiguration $parentBreadcrumbConfiguration,
    ) {
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getParameters(): StringMap
    {
        return $this->parameters;
    }

    public function getParentBreadcrumbConfiguration(): ?ParentBreadcrumbConfiguration
    {
        return $this->parentBreadcrumbConfiguration;
    }
}
