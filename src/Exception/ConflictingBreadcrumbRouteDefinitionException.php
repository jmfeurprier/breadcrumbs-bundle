<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

class ConflictingBreadcrumbRouteDefinitionException extends BreadcrumbConfigurationException
{
    /**
     * @param string[] $routeNames
     */
    public function __construct(
        private readonly array $routeNames,
    ) {
        parent::__construct(
            message: sprintf(
                "Breadcrumb route(s) defined in both path files and inline configuration: %s.",
                implode(', ', $this->routeNames),
            ),
        );
    }

    /**
     * @return string[]
     */
    public function getRouteNames(): array
    {
        return $this->routeNames;
    }
}
