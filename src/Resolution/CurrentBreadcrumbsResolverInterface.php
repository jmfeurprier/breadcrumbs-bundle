<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Exception\BreadcrumbCircularReferenceException;
use Jmf\Breadcrumbs\Exception\BreadcrumbContextResolutionException;
use Jmf\Breadcrumbs\Exception\BreadcrumbLabelRenderingException;
use Jmf\Breadcrumbs\Exception\BreadcrumbRouteParametersResolutionException;
use Jmf\Breadcrumbs\Exception\NoMainRequestException;
use Jmf\Breadcrumbs\Model\BreadcrumbCollection;

interface CurrentBreadcrumbsResolverInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @throws BreadcrumbLabelRenderingException
     * @throws BreadcrumbCircularReferenceException
     * @throws BreadcrumbContextResolutionException
     * @throws BreadcrumbRouteParametersResolutionException
     * @throws NoMainRequestException
     */
    public function resolve(array $context): BreadcrumbCollection;
}
