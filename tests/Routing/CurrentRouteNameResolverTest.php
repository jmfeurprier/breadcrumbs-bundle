<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Routing;

use Jmf\Breadcrumbs\Exception\NoMainRequestException;
use Jmf\Breadcrumbs\Routing\CurrentRouteNameResolver;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\InvalidArgumentException;

final class CurrentRouteNameResolverTest extends TestCase
{
    private CurrentRouteNameResolver $resolver;

    private RequestStack & Stub $requestStack;

    private string $result;

    protected function setUp(): void
    {
        $this->requestStack = $this->createStub(RequestStack::class);

        $this->resolver = new CurrentRouteNameResolver($this->requestStack);
    }

    public function testResolveReturnsRouteName(): void
    {
        $this->givenMainRequest(
            $this->createRequest('app_home'),
        );

        $this->whenResolve();

        $this->thenResult('app_home');
    }

    public function testResolveThrowsWhenNoMainRequest(): void
    {
        $this->requestStack->method('getMainRequest')->willReturn(null);

        $this->expectException(NoMainRequestException::class);

        $this->whenResolve();
    }

    public function testResolveThrowsWhenRouteNameIsEmpty(): void
    {
        $this->givenMainRequest(
            $this->createRequest(''),
        );

        $this->expectException(InvalidArgumentException::class);

        $this->whenResolve();
    }

    private function givenMainRequest(Request $request): void
    {
        $this->requestStack->method('getMainRequest')->willReturn($request);
    }

    private function createRequest(string $routeName): Request
    {
        $request = Request::create('/');
        $request->attributes->set('_route', $routeName);

        return $request;
    }

    private function whenResolve(): void
    {
        $this->result = $this->resolver->resolve();
    }

    private function thenResult(string $routeName): void
    {
        self::assertSame($routeName, $this->result);
    }
}
