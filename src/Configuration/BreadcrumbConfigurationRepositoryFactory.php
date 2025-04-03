<?php

namespace Jmf\Breadcrumbs\Configuration;

use Override;

readonly class BreadcrumbConfigurationRepositoryFactory implements BreadcrumbConfigurationRepositoryFactoryInterface
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private BreadcrumbConfigurationsLoader $breadcrumbConfigurationsLoader,
        private array $config,
    ) {
    }

    #[Override]
    public function make(): BreadcrumbConfigurationRepositoryInterface
    {
        return new BreadcrumbConfigurationRepository(
            $this->getBreadcrumbConfigurations(),
        );
    }

    /**
     * @return BreadcrumbConfiguration[]
     */
    private function getBreadcrumbConfigurations(): iterable
    {
        return $this->breadcrumbConfigurationsLoader->load($this->config);
    }
}
