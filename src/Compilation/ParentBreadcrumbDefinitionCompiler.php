<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Compilation;

use Jmf\Breadcrumbs\Definition\ParentBreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\StringMap;
use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Jmf\Breadcrumbs\Exception\MissingBreadcrumbParentRouteException;
use Webmozart\Assert\Assert;

readonly class ParentBreadcrumbDefinitionCompiler
{
    /**
     * @param non-empty-string     $routeName
     * @param array<string, mixed> $config
     *
     * @throws BreadcrumbConfigurationException
     */
    public function compile(
        string $routeName,
        array $config,
    ): ?ParentBreadcrumbDefinition {
        if (!array_key_exists('parent', $config)) {
            return null;
        }

        $parentConfig = $config['parent'];

        Assert::isMap($parentConfig);

        return new ParentBreadcrumbDefinition(
            $this->getRouteName(
                $routeName,
                $parentConfig,
            ),
            $this->getParameters(
                $parentConfig,
            ),
        );
    }

    /**
     * @param non-empty-string     $routeName
     * @param array<string, mixed> $parentConfig
     *
     * @return non-empty-string
     *
     * @throws MissingBreadcrumbParentRouteException
     */
    private function getRouteName(
        string $routeName,
        array $parentConfig,
    ): string {
        if (!array_key_exists('route', $parentConfig)) {
            throw new MissingBreadcrumbParentRouteException(
                routeName: $routeName,
            );
        }

        $route = $parentConfig['route'];

        Assert::stringNotEmpty($route);

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
