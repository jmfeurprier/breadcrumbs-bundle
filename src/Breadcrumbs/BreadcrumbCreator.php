<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Configuration\BreadcrumbConfiguration;
use Jmf\Breadcrumbs\Exception\BreadcrumbLabelRenderingException;
use Jmf\Breadcrumbs\Exception\BreadcrumbUrlRenderingException;

readonly class BreadcrumbCreator
{
    public function __construct(
        private BreadcrumbLabelRenderer $breadcrumbLabelRenderer,
        private BreadcrumbUrlRenderer $breadcrumbUrlRenderer,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws BreadcrumbLabelRenderingException
     * @throws BreadcrumbUrlRenderingException
     */
    public function create(
        BreadcrumbConfiguration $breadcrumbConfiguration,
        array $context,
    ): Breadcrumb {
        return new Breadcrumb(
            label:     $this->breadcrumbLabelRenderer->render(
                           $breadcrumbConfiguration,
                           $context,
                       ),
            path:      $this->breadcrumbUrlRenderer->render(
                           $breadcrumbConfiguration,
                           $context,
                       ),
            routeName: $breadcrumbConfiguration->getRouteName(),
        );
    }
}
