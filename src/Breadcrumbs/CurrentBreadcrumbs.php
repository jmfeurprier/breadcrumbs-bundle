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

    public function getBreadcrumbs(): iterable
    {
        return $this->breadcrumbs;
    }
}
