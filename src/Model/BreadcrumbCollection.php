<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Model;

use Webmozart\Assert\Assert;

readonly class BreadcrumbCollection
{
    /**
     * @param Breadcrumb[] $breadcrumbs
     */
    public function __construct(
        private array $breadcrumbs,
    ) {
        Assert::allIsInstanceOf($this->breadcrumbs, Breadcrumb::class);
    }

    /**
     * @return Breadcrumb[]
     */
    public function all(): array
    {
        return $this->breadcrumbs;
    }

    public function tryGetCurrentBreadcrumb(): ?Breadcrumb
    {
        if (count($this->breadcrumbs) >= 1) {
            return array_slice($this->breadcrumbs, -1, 1)[0];
        }

        return null;
    }

    public function tryGetPreviousBreadcrumb(): ?Breadcrumb
    {
        if (count($this->breadcrumbs) >= 2) {
            return array_slice($this->breadcrumbs, -2, 1)[0];
        }

        return null;
    }
}
