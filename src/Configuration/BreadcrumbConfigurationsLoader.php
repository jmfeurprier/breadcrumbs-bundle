<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Configuration;

use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
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
     *
     * @throws BreadcrumbConfigurationException
     */
    public function load(
        array $config,
    ): iterable {
        Assert::isMap($config);

        $breadcrumbConfigurations = [];

        foreach ($config as $routeName => $breadcrumbConfig) {
            Assert::isMap($breadcrumbConfig);

            $breadcrumbConfigurations[] = $this->breadcrumbConfigurationLoader->load($routeName, $breadcrumbConfig);
        }

        return $breadcrumbConfigurations;
    }
}
