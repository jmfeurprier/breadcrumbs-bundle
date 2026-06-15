<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

class BreadcrumbRouteDefinitionConflictException extends BreadcrumbConfigurationException
{
    /**
     * @param string[] $routeNames
     */
    public function __construct(
        private readonly array $routeNames,
    ) {
        parent::__construct(
            message: sprintf(
                "Breadcrumb route(s) defined more than once in configuration: %s.",
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
