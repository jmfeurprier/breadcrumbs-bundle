<?php

namespace Jmf\Breadcrumbs\Configuration;

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

        return new ParentBreadcrumbConfiguration(
            $this->getRouteName($config),
            $this->getParameters($config),
        );
    }

    private function getRouteName(array $config): string
    {
        return $config['parent']['route']; // @todo
    }

    private function getParameters(array $config): KeyStringCollection
    {
        return new KeyStringCollection(
            $config['parent']['parameters'],
        );
    }
}
