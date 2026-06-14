<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbResolutionException;
use Jmf\Breadcrumbs\Model\Breadcrumb;

interface PreviousBreadcrumbResolverInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @throws PreviousBreadcrumbResolutionException
     */
    public function resolve(array $context): Breadcrumb;
}
