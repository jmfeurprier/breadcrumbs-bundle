<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

class BackUrlResolver
{
    /**
     * @var array<string, mixed>
     */
    private array $parameters;

    /**
     * @param array<string, array<string, mixed>> $breadcrumbDefinitions
     */
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly PropertyAccessorInterface $propertyAccessor,
        private readonly array $breadcrumbDefinitions,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function resolve(array $context): string
    {
        $this->parameters     = $context;
        $routeName            = $this->getRouteName();
        $breadcrumbDefinition = $this->breadcrumbDefinitions[$routeName] ?? null;

        if (!$breadcrumbDefinition) {
            throw new RuntimeException("Missing breadcrumb definition for route '{$routeName}'.");
        }

        if (!array_key_exists('parent', $breadcrumbDefinition)) {
            throw new RuntimeException("Missing breadcrumb 'parent' definition for route '{$routeName}'.");
        }

        foreach ($breadcrumbDefinition['parent']['parameters'] ?? [] as $key => $value) {
            Assert::string($key);

            $this->parameters[$key] = $this->propertyAccessor->getValue((object) $this->parameters, $value);
        }

        $routeName            = $breadcrumbDefinition['parent']['route'];
        $breadcrumbDefinition = $this->breadcrumbDefinitions[$routeName] ?? null;

        if (!$breadcrumbDefinition) {
            throw new RuntimeException("Parent breadcrumb definition for route '{$routeName}' is not defined.");
        }

        return $this->buildUrl($routeName, $breadcrumbDefinition);
    }

    /**
     * @param array{'parameters'?: array<string, string>} $definition
     */
    private function buildUrl(
        string $routeName,
        array $definition
    ): string {
        $parameters = [];

        foreach ($definition['parameters'] ?? [] as $key => $value) {
            Assert::string($key);
            Assert::string($value);

            $parameters[$key] = $this->propertyAccessor->getValue((object) $this->parameters, $value);
        }

        return $this->urlGenerator->generate(
            $routeName,
            $parameters
        );
    }

    private function getRouteName(): string
    {
        $request   = $this->getRequest();
        $routeName = $request->attributes->get('_route');

        Assert::string($routeName);

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
}
