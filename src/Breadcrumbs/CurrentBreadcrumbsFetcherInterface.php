<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

interface CurrentBreadcrumbsFetcherInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function fetch(array $context): CurrentBreadcrumbs;
}
