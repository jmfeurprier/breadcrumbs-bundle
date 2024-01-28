<?php

namespace Jmf\Breadcrumbs\Configuration;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

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

    /**
     * @param array<string, mixed> $config
     */
    private function getLabel(array $config): string
    {
        if (!array_key_exists('label', $config)) {
            throw new InvalidArgumentException("Missing breadcrumb 'label' configuration.");
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

        Assert::isArray($parametersConfig);

        return new KeyStringCollection($parametersConfig);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function getParentBreadcrumbConfiguration(array $config): ?ParentBreadcrumbConfiguration
    {
        return $this->parentBreadcrumbConfigurationLoader->load($config);
    }
}
