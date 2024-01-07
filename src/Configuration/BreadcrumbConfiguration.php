<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

readonly class BreadcrumbConfiguration
{
    public function __construct(
        private string $routeName,
        private string $label,
        private KeyStringCollection $parameters,
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

    public function getParameters(): KeyStringCollection
    {
        return $this->parameters;
    }

    public function getParentBreadcrumbConfiguration(): ?ParentBreadcrumbConfiguration
    {
        return $this->parentBreadcrumbConfiguration;
    }
}
