<?php

namespace Jmf\Breadcrumbs\Configuration;

use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;

interface BreadcrumbConfigurationRepositoryFactoryInterface
{
    /**
     * @throws BreadcrumbConfigurationException
     */
    public function make(): BreadcrumbConfigurationRepositoryInterface;
}
