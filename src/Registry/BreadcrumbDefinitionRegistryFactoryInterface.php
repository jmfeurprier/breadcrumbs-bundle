<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Registry;

use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;

interface BreadcrumbDefinitionRegistryFactoryInterface
{
    /**
     * @throws BreadcrumbConfigurationException
     */
    public function create(): BreadcrumbDefinitionRegistryInterface;
}
