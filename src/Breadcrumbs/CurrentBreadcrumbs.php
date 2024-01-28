<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

readonly class CurrentBreadcrumbs
{
    /**
     * @param Breadcrumb[] $breadcrumbs
     */
    public function __construct(
        private iterable $breadcrumbs,
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
        $breadcrumbs = iterator_to_array($this->breadcrumbs);

        if (count($breadcrumbs) >= 1) {
            return array_slice($breadcrumbs, -1, 1)[0];
        }

        return null;
    }

    public function tryGetPreviousBreadcrumb(): ?Breadcrumb
    {
        $breadcrumbs = iterator_to_array($this->breadcrumbs);

        if (count($breadcrumbs) >= 2) {
            return array_slice($breadcrumbs, -2, 1)[0];
        }

        return null;
    }
}
