<?php

namespace Jmf\Breadcrumbs\Configuration;

use DomainException;
use Override;
use Webmozart\Assert\Assert;

readonly class BreadcrumbConfigurationRepository implements BreadcrumbConfigurationRepositoryInterface
{
    /**
     * @var array<string, BreadcrumbConfiguration>
     */
    private array $indexedByRouteName;

    /**
     * @param BreadcrumbConfiguration[] $breadcrumbConfigurations
     */
    public function __construct(
        iterable $breadcrumbConfigurations,
    ) {
        Assert::allIsInstanceOf($breadcrumbConfigurations, BreadcrumbConfiguration::class);

        $indexed = [];

        foreach ($breadcrumbConfigurations as $breadcrumbConfiguration) {
            $indexed[$breadcrumbConfiguration->getRouteName()] = $breadcrumbConfiguration;
        }

        $this->indexedByRouteName = $indexed;
    }

    #[Override]
    public function tryGet(string $routeName): ?BreadcrumbConfiguration
    {
        return $this->indexedByRouteName[$routeName] ?? null;
    }
}
