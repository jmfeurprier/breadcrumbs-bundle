<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Registry;

use Jmf\Breadcrumbs\Compilation\BreadcrumbDefinitionsCompiler;
use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Override;

readonly class BreadcrumbDefinitionRegistryFactory implements BreadcrumbDefinitionRegistryFactoryInterface
{
    /**
     * @param array<non-empty-string, mixed> $config
     */
    public function __construct(
        private BreadcrumbDefinitionsCompiler $breadcrumbDefinitionsCompiler,
        private array $config,
    ) {
    }

    #[Override]
    public function create(): BreadcrumbDefinitionRegistryInterface
    {
        return new BreadcrumbDefinitionRegistry(
            $this->getBreadcrumbDefinitions(),
        );
    }

    /**
     * @return BreadcrumbDefinition[]
     *
     * @throws BreadcrumbConfigurationException
     */
    private function getBreadcrumbDefinitions(): iterable
    {
        return $this->breadcrumbDefinitionsCompiler->compile($this->config);
    }
}
