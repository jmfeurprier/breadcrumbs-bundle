<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Configuration;

interface BreadcrumbConfigurationRepositoryInterface
{
    public function tryGet(string $routeName): ?BreadcrumbConfiguration;
}
