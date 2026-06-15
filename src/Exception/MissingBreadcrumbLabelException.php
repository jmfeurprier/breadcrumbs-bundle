<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

class MissingBreadcrumbLabelException extends BreadcrumbConfigurationException
{
    /**
     * @param non-empty-string $routeName
     */
    public function __construct(
        private readonly string $routeName,
    ) {
        parent::__construct(
            message: sprintf("Missing 'label' configuration for breadcrumb route '%s'.", $this->routeName),
        );
    }

    /**
     * @return non-empty-string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }
}
