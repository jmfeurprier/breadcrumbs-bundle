<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Exception\InvalidCurrentBreadcrumbNotFoundBehaviorException;
use Jmf\Breadcrumbs\Registry\BreadcrumbDefinitionRegistryInterface;
use Jmf\Breadcrumbs\Routing\CurrentRouteNameResolver;

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
     * @throws InvalidCurrentBreadcrumbNotFoundBehaviorException
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
     * @throws InvalidCurrentBreadcrumbNotFoundBehaviorException
     */
    private function getBreadcrumbNotFoundBehavior(): CurrentBreadcrumbNotFoundBehavior
    {
        $behavior = CurrentBreadcrumbNotFoundBehavior::tryFrom(
            $this->currentBreadcrumbNotFoundStrategy,
        );

        if (!$behavior instanceof CurrentBreadcrumbNotFoundBehavior) {
            throw new InvalidCurrentBreadcrumbNotFoundBehaviorException(
                value:       $this->currentBreadcrumbNotFoundStrategy,
                validValues: array_map(
                    static fn (CurrentBreadcrumbNotFoundBehavior $case): string => $case->value,
                    CurrentBreadcrumbNotFoundBehavior::cases(),
                ),
            );
        }

        return $behavior;
    }
}
