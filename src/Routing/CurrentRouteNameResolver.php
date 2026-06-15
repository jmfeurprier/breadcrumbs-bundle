<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Routing;

use Jmf\Breadcrumbs\Exception\NoMainRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

readonly class CurrentRouteNameResolver
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @return non-empty-string
     *
     * @throws NoMainRequestException
     */
    public function resolve(): string
    {
        $routeName = $this->getRequest()->attributes->get('_route');

        Assert::stringNotEmpty($routeName, 'Failed retrieving current route name.');

        return $routeName;
    }

    /**
     * @throws NoMainRequestException
     */
    private function getRequest(): Request
    {
        $request = $this->requestStack->getMainRequest();

        if ($request instanceof Request) {
            return $request;
        }

        throw new NoMainRequestException();
    }
}
