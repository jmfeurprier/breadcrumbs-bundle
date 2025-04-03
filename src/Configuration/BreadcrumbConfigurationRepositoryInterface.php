<?php

namespace Jmf\Breadcrumbs\Configuration;

interface BreadcrumbConfigurationRepositoryInterface
{
    public function tryGet(string $routeName): ?BreadcrumbConfiguration;
}
