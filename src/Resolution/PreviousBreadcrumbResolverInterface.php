<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbNotFoundException;
use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbResolutionFailedException;
use Jmf\Breadcrumbs\Model\Breadcrumb;

interface PreviousBreadcrumbResolverInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @throws PreviousBreadcrumbNotFoundException
     * @throws PreviousBreadcrumbResolutionFailedException
     */
    public function resolve(array $context): Breadcrumb;

    /**
     * @param array<string, mixed> $context
     *
     * @throws PreviousBreadcrumbResolutionFailedException
     */
    public function tryResolve(array $context): ?Breadcrumb;
}
