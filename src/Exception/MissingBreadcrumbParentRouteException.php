<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

class MissingBreadcrumbParentRouteException extends BreadcrumbConfigurationException
{
    public function __construct()
    {
        parent::__construct(
            message: "Missing 'route' configuration for breadcrumb parent.",
        );
    }
}
