<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Definition;

readonly class BreadcrumbDefinition
{
    /**
     * @param non-empty-string $routeName
     */
    public function __construct(
        private string $routeName,
        private string $label,
        private StringMap $parameters,
        private ?ParentBreadcrumbDefinition $parentBreadcrumbDefinition,
    ) {
    }

    /**
     * @return non-empty-string
     */
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

    public function getParentBreadcrumbDefinition(): ?ParentBreadcrumbDefinition
    {
        return $this->parentBreadcrumbDefinition;
    }
}
