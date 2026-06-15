<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Registry;

use Jmf\Breadcrumbs\Compilation\BreadcrumbDefinitionCollectionCompiler;
use Override;

readonly class BreadcrumbDefinitionRegistryFactory implements BreadcrumbDefinitionRegistryFactoryInterface
{
    /**
     * @param array<non-empty-string, mixed> $config
     */
    public function __construct(
        private BreadcrumbDefinitionCollectionCompiler $breadcrumbDefinitionCollectionCompiler,
        private array $config,
    ) {
    }

    #[Override]
    public function create(): BreadcrumbDefinitionRegistryInterface
    {
        return new BreadcrumbDefinitionRegistry(
            $this->breadcrumbDefinitionCollectionCompiler->compile(
                $this->config,
            ),
        );
    }
}
