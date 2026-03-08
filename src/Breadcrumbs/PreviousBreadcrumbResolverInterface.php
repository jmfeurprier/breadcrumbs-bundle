<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbResolutionException;

interface PreviousBreadcrumbResolverInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @throws PreviousBreadcrumbResolutionException
     */
    public function resolve(array $context): Breadcrumb;
}
