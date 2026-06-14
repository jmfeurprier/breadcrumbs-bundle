<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\ParentBreadcrumbDefinition;
use Jmf\Breadcrumbs\Model\CurrentBreadcrumbs;
use Jmf\Breadcrumbs\Repository\BreadcrumbDefinitionRepositoryInterface;
use Override;

readonly class CurrentBreadcrumbsFetcher implements CurrentBreadcrumbsFetcherInterface
{
    public function __construct(
        private RouteNameResolver $routeNameResolver,
        private BreadcrumbDefinitionRepositoryInterface $breadcrumbDefinitionRepository,
        private ContextResolver $contextResolver,
        private BreadcrumbCreator $breadcrumbCreator,
    ) {
    }

    #[Override]
    public function fetch(array $context): CurrentBreadcrumbs
    {
        $breadcrumbs = [];
        $routeName   = $this->getRouteName();

        while (true) {
            $breadcrumbDefinition = $this->breadcrumbDefinitionRepository->tryGet($routeName);

            if (!$breadcrumbDefinition instanceof BreadcrumbDefinition) {
                break;
            }

            $context = $this->contextResolver->resolve(
                $context,
                $breadcrumbDefinition->getParameters()->all(),
            );

            $breadcrumbs[] = $this->breadcrumbCreator->create(
                $breadcrumbDefinition,
                $context,
            );

            if (
                !$breadcrumbDefinition->getParentBreadcrumbDefinition() instanceof ParentBreadcrumbDefinition
            ) {
                break;
            }

            $routeName = $breadcrumbDefinition->getParentBreadcrumbDefinition()->getRouteName();

            $context = $this->contextResolver->resolve(
                $context,
                $breadcrumbDefinition->getParentBreadcrumbDefinition()->getParameters()->all(),
            );
        }

        return new CurrentBreadcrumbs(array_reverse($breadcrumbs));
    }

    /**
     * @return non-empty-string
     */
    private function getRouteName(): string
    {
        return $this->routeNameResolver->resolve();
    }
}
