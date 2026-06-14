<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Exception\BreadcrumbRouteParametersResolutionException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Throwable;

readonly class BreadcrumbRouteParametersResolver
{
    public function __construct(
        private PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     *
     * @throws BreadcrumbRouteParametersResolutionException
     */
    public function resolve(
        BreadcrumbDefinition $breadcrumbDefinition,
        array $context,
    ): array {
        $routeParameters = [];

        try {
            foreach ($breadcrumbDefinition->getParameters()->all() as $key => $value) {
                $routeParameters[$key] = $this->propertyAccessor->getValue(
                    (object) $context,
                    $value,
                );
            }
        } catch (Throwable $e) {
            throw new BreadcrumbRouteParametersResolutionException(
                routeName: $breadcrumbDefinition->getRouteName(),
                label:     $breadcrumbDefinition->getLabel(),
                context:   $context,
                previous:  $e,
            );
        }

        return $routeParameters;
    }
}
