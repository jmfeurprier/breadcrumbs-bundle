<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

readonly class CurrentBreadcrumbs
{
    /**
     * @param Breadcrumb[] $breadcrumbs
     */
    public function __construct(
        private array $breadcrumbs,
    ) {
    }

    /**
     * @return Breadcrumb[]
     */
    public function getBreadcrumbs(): iterable
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
