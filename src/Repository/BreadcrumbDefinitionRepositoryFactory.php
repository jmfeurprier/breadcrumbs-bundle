<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Repository;

use Jmf\Breadcrumbs\Compilation\BreadcrumbDefinitionsCompiler;
use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Override;

readonly class BreadcrumbDefinitionRepositoryFactory implements BreadcrumbDefinitionRepositoryFactoryInterface
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
    public function create(): BreadcrumbDefinitionRepositoryInterface
    {
        return new BreadcrumbDefinitionRepository(
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
