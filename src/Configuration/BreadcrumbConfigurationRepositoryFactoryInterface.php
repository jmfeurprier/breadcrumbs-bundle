<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Configuration;

use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;

interface BreadcrumbConfigurationRepositoryFactoryInterface
{
    /**
     * @throws BreadcrumbConfigurationException
     */
    public function make(): BreadcrumbConfigurationRepositoryInterface;
}
