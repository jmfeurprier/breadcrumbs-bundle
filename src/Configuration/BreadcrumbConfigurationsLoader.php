<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Webmozart\Assert\Assert;

readonly class BreadcrumbConfigurationsLoader
{
    public function __construct(
        private BreadcrumbConfigurationLoader $breadcrumbConfigurationLoader,
    ) {
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return BreadcrumbConfiguration[]
     */
    public function load(
        array $config,
    ): iterable {
        Assert::isMap($config);

        $breadcrumbConfigurations = [];

        foreach ($config as $routeName => $breadcrumbConfig) {
            Assert::isArray($breadcrumbConfig);

            $breadcrumbConfigurations[] = $this->breadcrumbConfigurationLoader->load($routeName, $breadcrumbConfig);
        }

        return $breadcrumbConfigurations;
    }
}
