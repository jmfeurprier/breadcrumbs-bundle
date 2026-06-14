<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Definition\BreadcrumbConfiguration;
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
        BreadcrumbConfiguration $breadcrumbConfiguration,
        array $context,
    ): array {
        $routeParameters = [];

        try {
            foreach ($breadcrumbConfiguration->getParameters()->all() as $key => $value) {
                $routeParameters[$key] = $this->propertyAccessor->getValue(
                    (object) $context,
                    $value,
                );
            }
        } catch (Throwable $e) {
            throw new BreadcrumbRouteParametersResolutionException(
                routeName: $breadcrumbConfiguration->getRouteName(),
                label:     $breadcrumbConfiguration->getLabel(),
                context:   $context,
                previous:  $e,
            );
        }

        return $routeParameters;
    }
}
