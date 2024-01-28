<?php

namespace Jmf\Breadcrumbs\Configuration;

interface BreadcrumbConfigurationRepositoryFactoryInterface
{
    public function make(): BreadcrumbConfigurationRepositoryInterface;
}
