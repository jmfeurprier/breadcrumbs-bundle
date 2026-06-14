<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Repository;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;

interface BreadcrumbDefinitionRepositoryInterface
{
    public function tryGet(string $routeName): ?BreadcrumbDefinition;
}
