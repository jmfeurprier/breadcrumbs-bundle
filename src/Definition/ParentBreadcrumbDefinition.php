<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Definition;

readonly class ParentBreadcrumbDefinition
{
    public function __construct(
        private string $routeName,
        private StringMap $parameters,
    ) {
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getParameters(): StringMap
    {
        return $this->parameters;
    }
}
