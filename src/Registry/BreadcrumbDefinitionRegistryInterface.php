<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Registry;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;

interface BreadcrumbDefinitionRegistryInterface
{
    /**
     * @param non-empty-string $routeName
     */
    public function tryGet(string $routeName): ?BreadcrumbDefinition;
}
