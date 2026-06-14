<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Repository;

use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;

interface BreadcrumbConfigurationRepositoryFactoryInterface
{
    /**
     * @throws BreadcrumbConfigurationException
     */
    public function create(): BreadcrumbConfigurationRepositoryInterface;
}
