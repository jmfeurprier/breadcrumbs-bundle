<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Repository;

use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;

interface BreadcrumbDefinitionRepositoryFactoryInterface
{
    /**
     * @throws BreadcrumbConfigurationException
     */
    public function create(): BreadcrumbDefinitionRepositoryInterface;
}
