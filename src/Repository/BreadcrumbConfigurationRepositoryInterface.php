<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Repository;

use Jmf\Breadcrumbs\Definition\BreadcrumbConfiguration;

interface BreadcrumbConfigurationRepositoryInterface
{
    public function tryGet(string $routeName): ?BreadcrumbConfiguration;
}
