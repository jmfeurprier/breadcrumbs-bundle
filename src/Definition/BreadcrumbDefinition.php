<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Definition;

use Webmozart\Assert\Assert;

readonly class BreadcrumbDefinition
{
    /**
     * @param non-empty-string $routeName
     * @param non-empty-string $label
     */
    public function __construct(
        private string $routeName,
        private string $label,
        private StringMap $parameters,
        private ?ParentBreadcrumbDefinition $parentBreadcrumbDefinition,
    ) {
        Assert::stringNotEmpty($this->routeName);
        Assert::stringNotEmpty($this->label);
    }

    /**
     * @return non-empty-string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @return non-empty-string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    public function getParameters(): StringMap
    {
        return $this->parameters;
    }

    public function getParentBreadcrumbDefinition(): ?ParentBreadcrumbDefinition
    {
        return $this->parentBreadcrumbDefinition;
    }
}
