<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\ParentBreadcrumbDefinition;
use Jmf\Breadcrumbs\Model\CurrentBreadcrumbs;
use Jmf\Breadcrumbs\Exception\NoMainRequestException;
use Jmf\Breadcrumbs\Registry\BreadcrumbDefinitionRegistryInterface;
use Jmf\Breadcrumbs\Routing\CurrentRouteNameResolver;
use Override;

readonly class CurrentBreadcrumbsResolver implements CurrentBreadcrumbsResolverInterface
{
    public function __construct(
        private CurrentRouteNameResolver $currentRouteNameResolver,
        private BreadcrumbDefinitionRegistryInterface $breadcrumbDefinitionRegistry,
        private ContextResolver $contextResolver,
        private BreadcrumbCreator $breadcrumbCreator,
    ) {
    }

    #[Override]
    public function resolve(array $context): CurrentBreadcrumbs
    {
        $breadcrumbs = [];
        $routeName   = $this->getRouteName();

        while (true) {
            $breadcrumbDefinition = $this->breadcrumbDefinitionRegistry->tryGet($routeName);

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
     *
     * @throws NoMainRequestException
     */
    private function getRouteName(): string
    {
        return $this->currentRouteNameResolver->resolve();
    }
}
