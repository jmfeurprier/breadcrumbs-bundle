<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Compilation;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\ParentBreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\StringMap;
use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Webmozart\Assert\Assert;

readonly class BreadcrumbDefinitionCompiler
{
    public function __construct(
        private ParentBreadcrumbDefinitionCompiler $parentBreadcrumbDefinitionCompiler,
    ) {
    }

    /**
     * @param non-empty-string     $routeName
     * @param array<string, mixed> $config
     *
     * @throws BreadcrumbConfigurationException
     */
    public function compile(
        string $routeName,
        array $config,
    ): BreadcrumbDefinition {
        return new BreadcrumbDefinition(
            $routeName,
            $this->getLabel($config),
            $this->getParameters($config),
            $this->getParentBreadcrumbDefinition($config),
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
    private function getParameters(array $config): StringMap
    {
        if (!array_key_exists('parameters', $config)) {
            return StringMap::createEmpty();
        }

        $parametersConfig = $config['parameters'];

        Assert::isMap($parametersConfig);
        Assert::allString($parametersConfig);

        return new StringMap($parametersConfig);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws BreadcrumbConfigurationException
     */
    private function getParentBreadcrumbDefinition(array $config): ?ParentBreadcrumbDefinition
    {
        return $this->parentBreadcrumbDefinitionCompiler->compile($config);
    }
}
