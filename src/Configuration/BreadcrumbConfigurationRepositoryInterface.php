<?php

namespace Jmf\Breadcrumbs\Configuration;

interface BreadcrumbConfigurationRepositoryInterface
{
    public function get(string $routeName): BreadcrumbConfiguration;

    public function tryGet(string $routeName): ?BreadcrumbConfiguration;
}
