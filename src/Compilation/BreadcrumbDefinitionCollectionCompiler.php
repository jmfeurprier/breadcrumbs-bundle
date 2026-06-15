<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Compilation;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinitionCollection;
use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Webmozart\Assert\Assert;

readonly class BreadcrumbDefinitionCollectionCompiler
{
    public function __construct(
        private BreadcrumbDefinitionCompiler $breadcrumbDefinitionCompiler,
    ) {
    }

    /**
     * @param array<non-empty-string, mixed> $config
     *
     * @throws BreadcrumbConfigurationException
     */
    public function compile(
        array $config,
    ): BreadcrumbDefinitionCollection {
        Assert::isMap($config);

        $breadcrumbDefinitions = [];

        foreach ($config as $routeName => $breadcrumbConfig) {
            Assert::isMap($breadcrumbConfig);

            $breadcrumbDefinitions[] = $this->breadcrumbDefinitionCompiler->compile(
                $routeName,
                $breadcrumbConfig,
            );
        }

        return new BreadcrumbDefinitionCollection($breadcrumbDefinitions);
    }
}
