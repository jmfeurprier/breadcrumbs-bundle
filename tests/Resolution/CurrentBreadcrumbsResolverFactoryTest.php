<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Resolution;

use Jmf\Breadcrumbs\Exception\CurrentBreadcrumbNotFoundException;
use Jmf\Breadcrumbs\Exception\InvalidCurrentBreadcrumbNotFoundBehaviorException;
use Jmf\Breadcrumbs\Registry\BreadcrumbDefinitionRegistryInterface;
use Jmf\Breadcrumbs\Resolution\BreadcrumbCreator;
use Jmf\Breadcrumbs\Resolution\ContextResolver;
use Jmf\Breadcrumbs\Resolution\CurrentBreadcrumbsResolverFactory;
use Jmf\Breadcrumbs\Routing\CurrentRouteNameResolver;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class CurrentBreadcrumbsResolverFactoryTest extends TestCase
{
    private CurrentRouteNameResolver & Stub $currentRouteNameResolver;

    private BreadcrumbDefinitionRegistryInterface & Stub $breadcrumbDefinitionRegistry;

    private ContextResolver & Stub $contextResolver;

    private BreadcrumbCreator & Stub $breadcrumbCreator;

    protected function setUp(): void
    {
        $this->currentRouteNameResolver     = $this->createStub(CurrentRouteNameResolver::class);
        $this->breadcrumbDefinitionRegistry = $this->createStub(BreadcrumbDefinitionRegistryInterface::class);
        $this->contextResolver              = $this->createStub(ContextResolver::class);
        $this->breadcrumbCreator            = $this->createStub(BreadcrumbCreator::class);

        $this->currentRouteNameResolver->method('resolve')->willReturn('route_unknown');
        $this->breadcrumbDefinitionRegistry->method('tryGet')->willReturn(null);
    }

    public function testCreateConvertsFailStringToEnumAndAppliesItToResolver(): void
    {
        $currentBreadcrumbsResolver = $this->makeFactory('fail')->create();

        $this->expectException(CurrentBreadcrumbNotFoundException::class);

        $currentBreadcrumbsResolver->resolve([]);
    }

    public function testCreateConvertsHideStringToEnumAndAppliesItToResolver(): void
    {
        $currentBreadcrumbsResolver = $this->makeFactory('hide')->create();

        $breadcrumbCollection = $currentBreadcrumbsResolver->resolve([]);

        self::assertSame([], $breadcrumbCollection->all());
    }

    public function testCreateThrowsOnInvalidBehaviorString(): void
    {
        $this->expectException(InvalidCurrentBreadcrumbNotFoundBehaviorException::class);

        try {
            $this->makeFactory('invalid')->create();
        } catch (InvalidCurrentBreadcrumbNotFoundBehaviorException $e) {
            self::assertSame('invalid', $e->getValue());
            self::assertSame(['fail', 'hide'], $e->getValidValues());

            throw $e;
        }
    }

    private function makeFactory(
        string $currentBreadcrumbNotFoundStrategy,
    ): CurrentBreadcrumbsResolverFactory {
        return new CurrentBreadcrumbsResolverFactory(
            currentRouteNameResolver:          $this->currentRouteNameResolver,
            breadcrumbDefinitionRegistry:       $this->breadcrumbDefinitionRegistry,
            contextResolver:                    $this->contextResolver,
            breadcrumbCreator:                  $this->breadcrumbCreator,
            currentBreadcrumbNotFoundStrategy:  $currentBreadcrumbNotFoundStrategy,
        );
    }
}
