<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Configuration\BreadcrumbConfiguration;
use Jmf\Breadcrumbs\Exception\BreadcrumbUrlRenderingException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Throwable;

readonly class BreadcrumbUrlRenderer
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws BreadcrumbUrlRenderingException
     */
    public function render(
        BreadcrumbConfiguration $breadcrumbConfiguration,
        array $context,
    ): string {
        $routeParameters = [];

        try {
            foreach ($breadcrumbConfiguration->getParameters()->all() as $key => $value) {
                $routeParameters[$key] = $this->propertyAccessor->getValue((object) $context, $value);
            }

            return $this->urlGenerator->generate(
                $breadcrumbConfiguration->getRouteName(),
                $routeParameters,
            );
        } catch (Throwable $e) {
            throw new BreadcrumbUrlRenderingException(
                routeName: $breadcrumbConfiguration->getRouteName(),
                label:     $breadcrumbConfiguration->getLabel(),
                context:   $context,
                previous:  $e,
            );
        }
    }
}
