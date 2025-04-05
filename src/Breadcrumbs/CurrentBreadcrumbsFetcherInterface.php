<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Exception\BreadcrumbsException;

interface CurrentBreadcrumbsFetcherInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @throws BreadcrumbsException
     */
    public function fetch(array $context): CurrentBreadcrumbs;
}
