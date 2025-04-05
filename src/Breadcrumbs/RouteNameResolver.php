<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

readonly class RouteNameResolver
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function resolve(): string
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
}
