<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Configuration;

use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Webmozart\Assert\Assert;

readonly class BreadcrumbConfigurationLoader
{
    public function __construct(
        private ParentBreadcrumbConfigurationLoader $parentBreadcrumbConfigurationLoader,
    ) {
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws BreadcrumbConfigurationException
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

    /**
     * @param array<string, mixed> $config
     *
     * @throws BreadcrumbConfigurationException
     */
    private function getLabel(array $config): string
    {
        if (!array_key_exists('label', $config)) {
            throw new BreadcrumbConfigurationException("Missing breadcrumb 'label' configuration.");
        }

        $label = $config['label'];

        Assert::string($label);

        return $label;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function getParameters(array $config): KeyStringCollection
    {
        if (!array_key_exists('parameters', $config)) {
            return KeyStringCollection::createEmpty();
        }

        $parametersConfig = $config['parameters'];

        Assert::isMap($parametersConfig);
        Assert::allString($parametersConfig);

        return new KeyStringCollection($parametersConfig);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws BreadcrumbConfigurationException
     */
    private function getParentBreadcrumbConfiguration(array $config): ?ParentBreadcrumbConfiguration
    {
        return $this->parentBreadcrumbConfigurationLoader->load($config);
    }
}
