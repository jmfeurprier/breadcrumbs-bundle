<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Exception\BreadcrumbsRuntimeException;
use Jmf\Breadcrumbs\Model\CurrentBreadcrumbs;

interface CurrentBreadcrumbsResolverInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @throws BreadcrumbsRuntimeException
     */
    public function resolve(array $context): CurrentBreadcrumbs;
}
