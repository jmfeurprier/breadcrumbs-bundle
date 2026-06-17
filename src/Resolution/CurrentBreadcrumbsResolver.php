<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\ParentBreadcrumbDefinition;
use Jmf\Breadcrumbs\Exception\BreadcrumbCircularReferenceException;
use Jmf\Breadcrumbs\Exception\BreadcrumbContextResolutionException;
use Jmf\Breadcrumbs\Exception\BreadcrumbLabelRenderingException;
use Jmf\Breadcrumbs\Exception\BreadcrumbRouteParametersResolutionException;
use Jmf\Breadcrumbs\Exception\CurrentBreadcrumbNotFoundException;
use Jmf\Breadcrumbs\Exception\NoMainRequestException;
use Jmf\Breadcrumbs\Model\Breadcrumb;
use Jmf\Breadcrumbs\Model\BreadcrumbCollection;
use Jmf\Breadcrumbs\Registry\BreadcrumbDefinitionRegistryInterface;
use Jmf\Breadcrumbs\Routing\CurrentRouteNameResolver;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
readonly class CurrentBreadcrumbsResolver implements CurrentBreadcrumbsResolverInterface
{
    public function __construct(
        private CurrentRouteNameResolver $currentRouteNameResolver,
        private BreadcrumbDefinitionRegistryInterface $breadcrumbDefinitionRegistry,
        private ContextResolver $contextResolver,
        private BreadcrumbCreator $breadcrumbCreator,
        private CurrentBreadcrumbNotFoundBehavior $currentBreadcrumbNotFoundBehavior,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws BreadcrumbLabelRenderingException
     * @throws BreadcrumbCircularReferenceException
     * @throws BreadcrumbContextResolutionException
     * @throws BreadcrumbRouteParametersResolutionException
     * @throws CurrentBreadcrumbNotFoundException
     * @throws NoMainRequestException
     */
    #[Override]
    public function resolve(array $context): BreadcrumbCollection
    {
        return new BreadcrumbCollection(
            $this->getBreadcrumbs(
                $this->getRouteName(),
                $context,
            ),
        );
    }

    /**
     * @param non-empty-string     $routeName
     * @param array<string, mixed> $context
     * @param array<string, true>  $visitedRouteNames
     *
     * @return Breadcrumb[]
     *
     * @throws BreadcrumbContextResolutionException
     * @throws BreadcrumbLabelRenderingException
     * @throws BreadcrumbRouteParametersResolutionException
     * @throws BreadcrumbCircularReferenceException
     * @throws CurrentBreadcrumbNotFoundException
     */
    private function getBreadcrumbs(
        string $routeName,
        array $context,
        array $visitedRouteNames = [],
    ): array {
        if (isset($visitedRouteNames[$routeName])) {
            throw new BreadcrumbCircularReferenceException(
                routeName: $routeName,
                context:   $context,
            );
        }

        $breadcrumbDefinition = $this->breadcrumbDefinitionRegistry->tryGet($routeName);

        if (!$breadcrumbDefinition instanceof BreadcrumbDefinition) {
            if ([] === $visitedRouteNames && CurrentBreadcrumbNotFoundBehavior::FAIL === $this->currentBreadcrumbNotFoundBehavior) {
                throw new CurrentBreadcrumbNotFoundException($context);
            }

            return [];
        }

        $visitedRouteNames[$routeName] = true;

        $context = $this->contextResolver->resolve(
            $context,
            $breadcrumbDefinition->getParameters()->all(),
        );

        $breadcrumb = $this->breadcrumbCreator->create(
            $breadcrumbDefinition,
            $context,
        );

        $parentBreadcrumbDefinition = $breadcrumbDefinition->getParentBreadcrumbDefinition();

        if (!$parentBreadcrumbDefinition instanceof ParentBreadcrumbDefinition) {
            return [$breadcrumb];
        }

        $context = $this->contextResolver->resolve(
            $context,
            $parentBreadcrumbDefinition->getParameters()->all(),
        );

        return [
            ...$this->getBreadcrumbs(
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
