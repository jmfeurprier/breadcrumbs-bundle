<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Exception\BreadcrumbLabelRenderingException;
use Jmf\Breadcrumbs\Exception\BreadcrumbRouteParametersResolutionException;
use Jmf\Breadcrumbs\Model\Breadcrumb;
use Jmf\Breadcrumbs\Rendering\BreadcrumbLabelRenderer;

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
        BreadcrumbDefinition $breadcrumbDefinition,
        array $context,
    ): Breadcrumb {
        return new Breadcrumb(
            label:           $this->breadcrumbLabelRenderer->render(
                     $breadcrumbDefinition,
                     $context,
                 ),
            routeName:       $breadcrumbDefinition->getRouteName(),
            routeParameters: $this->breadcrumbRouteParametersResolver->resolve(
                                 $breadcrumbDefinition,
                                 $context,
                             ),
        );
    }
}
