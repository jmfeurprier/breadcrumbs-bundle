<?php

namespace Jmf\Breadcrumbs\Configuration;

use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
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
     *
     * @throws BreadcrumbConfigurationException
     */
    private function getBreadcrumbConfigurations(): iterable
    {
        return $this->breadcrumbConfigurationsLoader->load($this->config);
    }
}
