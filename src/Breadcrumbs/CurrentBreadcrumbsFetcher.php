<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Configuration\BreadcrumbConfigurationRepositoryInterface;
use Override;

readonly class CurrentBreadcrumbsFetcher implements CurrentBreadcrumbsFetcherInterface
{
    public function __construct(
        private RouteNameResolver $routeNameResolver,
        private BreadcrumbConfigurationRepositoryInterface $breadcrumbConfigurationRepository,
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
            $breadcrumbConfiguration = $this->breadcrumbConfigurationRepository->tryGet($routeName);

            if (null === $breadcrumbConfiguration) {
                break;
            }

            $context = $this->contextResolver->resolve(
                $context,
                $breadcrumbConfiguration->getParameters()->all(),
            );

            $breadcrumbs[] = $this->breadcrumbCreator->create(
                $breadcrumbConfiguration,
                $context,
            );

            if (null === $breadcrumbConfiguration->getParentBreadcrumbConfiguration()) {
                break;
            }

            $routeName = $breadcrumbConfiguration->getParentBreadcrumbConfiguration()->getRouteName();

            $context = $this->contextResolver->resolve(
                $context,
                $breadcrumbConfiguration->getParentBreadcrumbConfiguration()->getParameters()->all(),
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
