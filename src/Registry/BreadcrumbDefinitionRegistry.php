<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Registry;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\BreadcrumbDefinitionCollection;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
readonly class BreadcrumbDefinitionRegistry implements BreadcrumbDefinitionRegistryInterface
{
    /**
     * @var array<non-empty-string, BreadcrumbDefinition>
     */
    private array $indexedByRouteName;

    public function __construct(
        BreadcrumbDefinitionCollection $breadcrumbDefinitionCollection,
    ) {
        $indexed = [];

        foreach ($breadcrumbDefinitionCollection->all() as $breadcrumbDefinition) {
            $indexed[$breadcrumbDefinition->getRouteName()] = $breadcrumbDefinition;
        }

        $this->indexedByRouteName = $indexed;
    }

    #[Override]
    public function tryGet(string $routeName): ?BreadcrumbDefinition
    {
        return $this->indexedByRouteName[$routeName] ?? null;
    }
}
