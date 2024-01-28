<?php

namespace Jmf\Breadcrumbs\Configuration;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

readonly class ParentBreadcrumbConfigurationLoader
{
    /**
     * @param array<string, mixed> $config
     */
    public function load(array $config): ?ParentBreadcrumbConfiguration
    {
        if (!array_key_exists('parent', $config)) {
            return null;
        }

        $parentConfig = $config['parent'];

        Assert::isMap($parentConfig);

        return new ParentBreadcrumbConfiguration(
            $this->getRouteName($parentConfig),
            $this->getParameters($parentConfig),
        );
    }

    /**
     * @param array<string, mixed> $parentConfig
     */
    private function getRouteName(array $parentConfig): string
    {
        if (!array_key_exists('route', $parentConfig)) {
            throw new InvalidArgumentException("Missing breadcrumb parent 'route' configuration.");
        }

        $route = $parentConfig['route'];

        Assert::string($route);

        return $route;
    }

    /**
     * @param array<string, mixed> $parentConfig
     */
    private function getParameters(array $parentConfig): KeyStringCollection
    {
        if (!array_key_exists('parameters', $parentConfig)) {
            return KeyStringCollection::createEmpty();
        }

        $parametersConfig = $parentConfig['parameters'];

        Assert::isArray($parametersConfig);

        return new KeyStringCollection($parametersConfig);
    }
}
