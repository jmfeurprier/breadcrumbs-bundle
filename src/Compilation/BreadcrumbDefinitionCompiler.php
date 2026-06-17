<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Compilation;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\ParentBreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\StringMap;
use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Jmf\Breadcrumbs\Exception\MissingBreadcrumbLabelException;
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
            routeName:                  $routeName,
            label:                      $this->getLabel(
                                            $routeName,
                                            $config,
                                        ),
            parameters:                 $this->getParameters(
                                            $config,
                                        ),
            parentBreadcrumbDefinition: $this->getParentBreadcrumbDefinition(
                                            $routeName,
                                            $config,
                                        ),
        );
    }

    /**
     * @param non-empty-string     $routeName
     * @param array<string, mixed> $config
     *
     * @throws MissingBreadcrumbLabelException
     */
    private function getLabel(
        string $routeName,
        array $config,
    ): string {
        if (!array_key_exists('label', $config)) {
            throw new MissingBreadcrumbLabelException(routeName: $routeName);
        }

        $label = $config['label'];

        Assert::stringNotEmpty($label);

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
     * @param non-empty-string     $routeName
     * @param array<string, mixed> $config
     *
     * @throws BreadcrumbConfigurationException
     */
    private function getParentBreadcrumbDefinition(
        string $routeName,
        array $config,
    ): ?ParentBreadcrumbDefinition {
        return $this->parentBreadcrumbDefinitionCompiler->compile(
            $routeName,
            $config,
        );
    }
}
