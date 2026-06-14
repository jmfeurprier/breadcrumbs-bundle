<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Exception\BreadcrumbsException;
use Jmf\Breadcrumbs\Model\CurrentBreadcrumbs;

interface CurrentBreadcrumbsFetcherInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @throws BreadcrumbsException
     */
    public function fetch(array $context): CurrentBreadcrumbs;
}
