<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Exception\BreadcrumbContextResolutionException;
use Jmf\Breadcrumbs\Exception\BreadcrumbLabelRenderingException;
use Jmf\Breadcrumbs\Exception\BreadcrumbRouteParametersResolutionException;
use Jmf\Breadcrumbs\Exception\BreadcrumbCircularReferenceException;
use Jmf\Breadcrumbs\Exception\NoMainRequestException;
use Jmf\Breadcrumbs\Model\Breadcrumb;
use Jmf\Breadcrumbs\Model\CurrentBreadcrumbs;
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
        return new CurrentBreadcrumbs(
            $this->resolveChain($this->getRouteName(), $context),
        );
    }

    /**
     * @param array<string, mixed>  $context
     * @param array<string, true>   $visitedRouteNames
     *
     * @return Breadcrumb[]
     *
     * @throws BreadcrumbContextResolutionException
     * @throws BreadcrumbLabelRenderingException
     * @throws BreadcrumbRouteParametersResolutionException
     * @throws BreadcrumbCircularReferenceException
     */
    private function resolveChain(
        string $routeName,
        array $context,
        array $visitedRouteNames = [],
    ): array {
        if (isset($visitedRouteNames[$routeName])) {
            throw new BreadcrumbCircularReferenceException(routeName: $routeName, context: $context);
        }

        $visitedRouteNames[$routeName] = true;

        $breadcrumbDefinition = $this->breadcrumbDefinitionRegistry->tryGet($routeName);

        if ($breadcrumbDefinition === null) {
            return [];
        }

        $context = $this->contextResolver->resolve(
            $context,
            $breadcrumbDefinition->getParameters()->all(),
        );

        $breadcrumb = $this->breadcrumbCreator->create(
            $breadcrumbDefinition,
            $context,
        );

        $parentBreadcrumbDefinition = $breadcrumbDefinition->getParentBreadcrumbDefinition();

        if ($parentBreadcrumbDefinition === null) {
            return [$breadcrumb];
        }

        $context = $this->contextResolver->resolve(
            $context,
            $parentBreadcrumbDefinition->getParameters()->all(),
        );

        return [
            ...$this->resolveChain(
                $parentBreadcrumbDefinition->getRouteName(),
                $context,
                $visitedRouteNames,
            ),
            $breadcrumb,
        ];
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
