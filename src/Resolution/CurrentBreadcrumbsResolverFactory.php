<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Registry\BreadcrumbDefinitionRegistryInterface;
use Jmf\Breadcrumbs\Routing\CurrentRouteNameResolver;
use Override;

readonly class CurrentBreadcrumbsResolverFactory
{
    public function __construct(
        private CurrentRouteNameResolver $currentRouteNameResolver,
        private BreadcrumbDefinitionRegistryInterface $breadcrumbDefinitionRegistry,
        private ContextResolver $contextResolver,
        private BreadcrumbCreator $breadcrumbCreator,
        private string $currentBreadcrumbNotFoundStrategy,
    ) {
    }

    /**
     * @throws \ValueError
     * @throws \TypeError
     */
    public function create(): CurrentBreadcrumbsResolverInterface
    {
        return new CurrentBreadcrumbsResolver(
            currentRouteNameResolver:          $this->currentRouteNameResolver,
            breadcrumbDefinitionRegistry:      $this->breadcrumbDefinitionRegistry,
            contextResolver:                   $this->contextResolver,
            breadcrumbCreator:                 $this->breadcrumbCreator,
            currentBreadcrumbNotFoundBehavior: $this->getBreadcrumbNotFoundBehavior(),
        );
    }

    /**
     * @return CurrentBreadcrumbNotFoundBehavior
     */
    private function getBreadcrumbNotFoundBehavior(): CurrentBreadcrumbNotFoundBehavior
    {
        return CurrentBreadcrumbNotFoundBehavior::from(
            $this->currentBreadcrumbNotFoundStrategy,
        );
    }
}
