<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Definition\BreadcrumbConfiguration;
use Jmf\Breadcrumbs\Exception\BreadcrumbLabelRenderingException;
use Jmf\Breadcrumbs\Exception\BreadcrumbRouteParametersResolutionException;

readonly class BreadcrumbCreator
{
    public function __construct(
        private BreadcrumbLabelRenderer $breadcrumbLabelRenderer,
        private BreadcrumbRouteParametersResolver $breadcrumbRouteParametersResolver,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws BreadcrumbLabelRenderingException
     * @throws BreadcrumbRouteParametersResolutionException
     */
    public function create(
        BreadcrumbConfiguration $breadcrumbConfiguration,
        array $context,
    ): Breadcrumb {
        return new Breadcrumb(
            label:           $this->breadcrumbLabelRenderer->render(
                $breadcrumbConfiguration,
                $context,
            ),
            routeName:       $breadcrumbConfiguration->getRouteName(),
            routeParameters: $this->breadcrumbRouteParametersResolver->resolve(
                $breadcrumbConfiguration,
                $context,
            ),
        );
    }
}
