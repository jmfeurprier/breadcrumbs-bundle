<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

use DomainException;
use Webmozart\Assert\Assert;

class BreadcrumbConfigurationRepository
{
    private array $indexedByRouteName = [];

    /**
     * @param BreadcrumbConfiguration[] $breadcrumbConfigurations
     */
    public function __construct(
        iterable $breadcrumbConfigurations,
    ) {
        Assert::allIsInstanceOf($breadcrumbConfigurations, BreadcrumbConfiguration::class);

        foreach ($breadcrumbConfigurations as $breadcrumbConfiguration) {
            $this->addConfiguration($breadcrumbConfiguration);
        }
    }

    private function addConfiguration(BreadcrumbConfiguration $breadcrumbConfiguration): void
    {
        $this->indexedByRouteName[$breadcrumbConfiguration->getRouteName()] = $breadcrumbConfiguration;
    }

    public function get(string $routeName): BreadcrumbConfiguration
    {
        return $this->tryGet($routeName) ?? throw new DomainException(); // @todo
    }

    public function tryGet(string $routeName): ?BreadcrumbConfiguration
    {
        return $this->indexedByRouteName[$routeName] ?? null;
    }
}
