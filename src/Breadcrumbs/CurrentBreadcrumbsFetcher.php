<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Configuration\BreadcrumbConfiguration;
use Jmf\Breadcrumbs\Configuration\BreadcrumbConfigurationRepositoryInterface;
use Jmf\Breadcrumbs\Exception\TemplateRenderingException;
use Jmf\Breadcrumbs\TemplateRendering\TemplateRendererInterface;
use Override;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

readonly class CurrentBreadcrumbsFetcher implements CurrentBreadcrumbsFetcherInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator,
        private TemplateRendererInterface $templateRenderer,
        private PropertyAccessorInterface $propertyAccessor,
        private BreadcrumbConfigurationRepositoryInterface $breadcrumbConfigurationRepository,
    ) {
    }

    #[Override]
    public function fetch(array $context): CurrentBreadcrumbs
    {
        $routeName   = $this->getRouteName();
        $breadcrumbs = [];
        $params      = $context;

        while (true) {
            $breadcrumbConfiguration = $this->breadcrumbConfigurationRepository->tryGet($routeName);

            if (null === $breadcrumbConfiguration) {
                break;
            }

            $breadcrumbDefinitionParameters = $breadcrumbConfiguration->getParameters()->all();

            foreach ($breadcrumbDefinitionParameters as $key => $value) {
                $params[$key] = $this->propertyAccessor->getValue((object) $params, $value);
            }

            $breadcrumbs[] = new Breadcrumb(
                $this->renderBreadcrumbLabel($breadcrumbConfiguration, $params),
                $this->renderBreadcrumbPath($routeName, $breadcrumbConfiguration, $params),
            );

            if (null === $breadcrumbConfiguration->getParentBreadcrumbConfiguration()) {
                break;
            }

            $parentParameters = $breadcrumbConfiguration->getParentBreadcrumbConfiguration()->getParameters()->all();

            foreach ($parentParameters as $key => $value) {
                $params[$key] = $this->propertyAccessor->getValue((object) $params, $value);
            }

            $routeName = $breadcrumbConfiguration->getParentBreadcrumbConfiguration()->getRouteName();
        }

        return new CurrentBreadcrumbs(array_reverse($breadcrumbs));
    }

    /**
     * @return non-empty-string
     */
    private function getRouteName(): string
    {
        $routeName = $this->getRequest()->attributes->get('_route');

        Assert::stringNotEmpty($routeName, 'Failed retrieving current route name.');

        return $routeName;
    }

    private function getRequest(): Request
    {
        $request = $this->requestStack->getMainRequest();

        if (null === $request) {
            throw new RuntimeException('No main request.');
        }

        return $request;
    }

    /**
     * @param array<string, mixed> $params
     *
     * @throws TemplateRenderingException
     */
    private function renderBreadcrumbLabel(
        BreadcrumbConfiguration $breadcrumbConfiguration,
        array $params,
    ): string {
        return $this->renderTemplateFromString(
            $breadcrumbConfiguration->getLabel(),
            $params,
        );
    }

    /**
     * @param array<string, mixed> $params
     */
    private function renderBreadcrumbPath(
        string $routeName,
        BreadcrumbConfiguration $breadcrumbConfiguration,
        array $params,
    ): string {
        $parameters = [];

        foreach ($breadcrumbConfiguration->getParameters()->all() as $key => $value) {
            $parameters[$key] = $this->propertyAccessor->getValue((object) $params, $value);
        }

        return $this->urlGenerator->generate(
            $routeName,
            $parameters,
        );
    }

    /**
     * @param array<string, mixed> $params
     *
     * @throws TemplateRenderingException
     */
    private function renderTemplateFromString(
        string $template,
        array $params,
    ): string {
        return $this->templateRenderer->renderFromString($template, $params);
    }
}
