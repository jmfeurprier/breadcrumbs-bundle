<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Compilation;

use Jmf\Breadcrumbs\Definition\ParentBreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\StringMap;
use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Webmozart\Assert\Assert;

readonly class ParentBreadcrumbDefinitionCompiler
{
    /**
     * @param array<string, mixed> $config
     *
     * @throws BreadcrumbConfigurationException
     */
    public function compile(array $config): ?ParentBreadcrumbDefinition
    {
        if (!array_key_exists('parent', $config)) {
            return null;
        }

        $parentConfig = $config['parent'];

        Assert::isMap($parentConfig);

        return new ParentBreadcrumbDefinition(
            $this->getRouteName($parentConfig),
            $this->getParameters($parentConfig),
        );
    }

    /**
     * @param array<string, mixed> $parentConfig
     *
     * @throws BreadcrumbConfigurationException
     */
    private function getRouteName(array $parentConfig): string
    {
        if (!array_key_exists('route', $parentConfig)) {
            throw new BreadcrumbConfigurationException("Missing breadcrumb parent 'route' configuration.");
        }

        $route = $parentConfig['route'];

        Assert::string($route);

        return $route;
    }

    /**
     * @param array<string, mixed> $parentConfig
     */
    private function getParameters(array $parentConfig): StringMap
    {
        if (!array_key_exists('parameters', $parentConfig)) {
            return StringMap::createEmpty();
        }

        $parametersConfig = $parentConfig['parameters'];

        Assert::isMap($parametersConfig);
        Assert::allString($parametersConfig);

        return new StringMap($parametersConfig);
    }
}
