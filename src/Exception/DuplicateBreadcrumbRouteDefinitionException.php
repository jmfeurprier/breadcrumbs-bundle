<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

class DuplicateBreadcrumbRouteDefinitionException extends BreadcrumbConfigurationException
{
    public function __construct(
        private readonly string $routeName,
    ) {
        parent::__construct(
            message: sprintf("Breadcrumb route '%s' is defined more than once across path files.", $this->routeName),
        );
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }
}
