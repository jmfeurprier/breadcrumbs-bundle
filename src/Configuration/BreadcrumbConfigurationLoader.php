<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

readonly class BreadcrumbConfigurationLoader
{
    public function __construct(
        private ParentBreadcrumbConfigurationLoader $parentBreadcrumbConfigurationLoader,
    ) {
    }

    /**
     * @param array<string, mixed> $config
     */
    public function load(
        string $routeName,
        array $config,
    ): BreadcrumbConfiguration {
        return new BreadcrumbConfiguration(
            $routeName,
            $this->getLabel($config),
            $this->getParameters($config),
            $this->getParentBreadcrumbConfiguration($config),
        );
    }

    private function getLabel(array $config): string
    {
        return $config['label']; // @todo
    }

    private function getParameters(array $config): KeyStringCollection
    {
        return new KeyStringCollection(
            $config['parameters'],
        );
    }

    private function getParentBreadcrumbConfiguration(array $config): ?ParentBreadcrumbConfiguration
    {
        return $this->parentBreadcrumbConfigurationLoader->load($config);
    }
}
