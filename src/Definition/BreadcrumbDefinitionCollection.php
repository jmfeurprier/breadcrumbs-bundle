<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Definition;

use Webmozart\Assert\Assert;

readonly class BreadcrumbDefinitionCollection
{
    /**
     * @param BreadcrumbDefinition[] $breadcrumbDefinitions
     */
    public function __construct(
        private array $breadcrumbDefinitions,
    ) {
        Assert::allIsInstanceOf($breadcrumbDefinitions, BreadcrumbDefinition::class);
    }

    /**
     * @return BreadcrumbDefinition[]
     */
    public function all(): iterable
    {
        yield from $this->breadcrumbDefinitions;
    }
}
