<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

interface BreadcrumbConfigurationRepositoryFactoryInterface
{
    public function make(): BreadcrumbConfigurationRepository;
}
