<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

class BreadcrumbCircularReferenceException extends BreadcrumbConfigurationException
{
    /**
     * @param non-empty-string $routeName
     */
    public function __construct(
        private readonly string $routeName,
    ) {
        parent::__construct(
            message: sprintf(
                         "Circular parent reference detected for breadcrumb route '%s'.",
                         $this->routeName,
                     ),
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
