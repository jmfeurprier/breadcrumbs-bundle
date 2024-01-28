<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Configuration\BreadcrumbConfiguration;
use Jmf\Breadcrumbs\Configuration\BreadcrumbConfigurationRepositoryInterface;
use Jmf\Breadcrumbs\Exception\TemplateRenderingException;
use Jmf\Breadcrumbs\TemplateRendering\TemplateRenderer;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

class CurrentBreadcrumbsFetcher implements CurrentBreadcrumbsFetcherInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $parameters;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly TemplateRenderer $templateRenderer,
        private readonly PropertyAccessorInterface $propertyAccessor,
        private readonly BreadcrumbConfigurationRepositoryInterface $breadcrumbConfigurationRepository,
    ) {
    }

    /**
     * @throws TemplateRenderingException
     */
    public function fetch(array $context): CurrentBreadcrumbs
    {
        $this->parameters = $context;
        $request          = $this->getRequest();
        $routeName        = $request->attributes->get('_route');
        $breadcrumbs      = [];

        Assert::string($routeName);

        while (true) {
            $breadcrumbConfiguration = $this->breadcrumbConfigurationRepository->tryGet($routeName);

            if (null === $breadcrumbConfiguration) {
                break; // @xxx
            }

            $breadcrumbDefinitionParameters = $breadcrumbConfiguration->getParameters()->all();

            foreach ($breadcrumbDefinitionParameters as $key => $value) {
                $this->parameters[$key] = $this->propertyAccessor->getValue((object) $this->parameters, $value);
            }

            $breadcrumbs[] = new Breadcrumb(
                $this->renderBreadcrumbLabel($breadcrumbConfiguration),
                $this->renderBreadcrumbPath($routeName, $breadcrumbConfiguration)
            );

            if (null === $breadcrumbConfiguration->getParentBreadcrumbConfiguration()) {
                break;
            }

            $this->parameters = $context;
            $parentParameters = $breadcrumbConfiguration->getParentBreadcrumbConfiguration()->getParameters()->all();

            foreach ($parentParameters as $key => $value) {
                $this->parameters[$key] = $this->propertyAccessor->getValue((object) $this->parameters, $value);
            }

            $routeName = $breadcrumbConfiguration->getParentBreadcrumbConfiguration()->getRouteName();
        }

        return new CurrentBreadcrumbs(array_reverse($breadcrumbs));
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
     * @throws TemplateRenderingException
     */
    private function renderBreadcrumbLabel(
        BreadcrumbConfiguration $breadcrumbConfiguration,
    ): string {
        return $this->renderTemplateFromString($breadcrumbConfiguration->getLabel());
    }

    private function renderBreadcrumbPath(
        string $routeName,
        BreadcrumbConfiguration $breadcrumbConfiguration,
    ): string {
        $parameters = [];

        foreach ($breadcrumbConfiguration->getParameters()->all() as $key => $value) {
            $parameters[$key] = $this->propertyAccessor->getValue((object) $this->parameters, $value);
        }

        return $this->urlGenerator->generate(
            $routeName,
            $parameters,
        );
    }

    /**
     * @throws TemplateRenderingException
     */
    private function renderTemplateFromString(string $template): string
    {
        return $this->templateRenderer->renderFromString($template, $this->parameters);
    }
}
