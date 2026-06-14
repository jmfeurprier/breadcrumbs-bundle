<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Definition;

readonly class ParentBreadcrumbConfiguration
{
    public function __construct(
        private string $routeName,
        private KeyStringCollection $parameters,
    ) {
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getParameters(): KeyStringCollection
    {
        return $this->parameters;
    }
}
