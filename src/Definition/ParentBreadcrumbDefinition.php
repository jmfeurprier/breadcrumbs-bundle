<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Definition;

readonly class ParentBreadcrumbDefinition
{
    /**
     * @param non-empty-string $routeName
     */
    public function __construct(
        private string $routeName,
        private StringMap $parameters,
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getParameters(): StringMap
    {
        return $this->parameters;
    }
}
