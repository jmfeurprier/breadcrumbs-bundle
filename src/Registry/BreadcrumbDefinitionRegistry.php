<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Registry;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Override;
use Webmozart\Assert\Assert;

readonly class BreadcrumbDefinitionRegistry implements BreadcrumbDefinitionRegistryInterface
{
    /**
     * @var array<non-empty-string, BreadcrumbDefinition>
     */
    private array $indexedByRouteName;

    /**
     * @param BreadcrumbDefinition[] $breadcrumbDefinitions
     */
    public function __construct(
        iterable $breadcrumbDefinitions,
    ) {
        Assert::allIsInstanceOf($breadcrumbDefinitions, BreadcrumbDefinition::class);

        $indexed = [];

        foreach ($breadcrumbDefinitions as $breadcrumbDefinition) {
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
