<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Registry;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;

interface BreadcrumbDefinitionRegistryInterface
{
    public function tryGet(string $routeName): ?BreadcrumbDefinition;
}
