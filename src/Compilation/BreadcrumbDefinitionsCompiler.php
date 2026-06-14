<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Compilation;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Webmozart\Assert\Assert;

readonly class BreadcrumbDefinitionsCompiler
{
    public function __construct(
        private BreadcrumbDefinitionCompiler $breadcrumbDefinitionCompiler,
    ) {
    }

    /**
     * @param array<non-empty-string, mixed> $config
     *
     * @return BreadcrumbDefinition[]
     *
     * @throws BreadcrumbConfigurationException
     */
    public function compile(
        array $config,
    ): iterable {
        Assert::isMap($config);

        $breadcrumbDefinitions = [];

        foreach ($config as $routeName => $breadcrumbConfig) {
            Assert::isMap($breadcrumbConfig);

            $breadcrumbDefinitions[] = $this->breadcrumbDefinitionCompiler->compile(
                $routeName,
                $breadcrumbConfig,
            );
        }

        return $breadcrumbDefinitions;
    }
}
