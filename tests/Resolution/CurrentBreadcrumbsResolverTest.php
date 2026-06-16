<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Resolution;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\ParentBreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\StringMap;
use Jmf\Breadcrumbs\Exception\BreadcrumbCircularReferenceException;
use Jmf\Breadcrumbs\Model\Breadcrumb;
use Jmf\Breadcrumbs\Model\CurrentBreadcrumbs;
use Jmf\Breadcrumbs\Registry\BreadcrumbDefinitionRegistryInterface;
use Jmf\Breadcrumbs\Resolution\BreadcrumbCreator;
use Jmf\Breadcrumbs\Resolution\ContextResolver;
use Jmf\Breadcrumbs\Resolution\CurrentBreadcrumbsResolver;
use Jmf\Breadcrumbs\Routing\CurrentRouteNameResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CurrentBreadcrumbsResolverTest extends TestCase
{
    private CurrentRouteNameResolver & MockObject $currentRouteNameResolver;

    private BreadcrumbDefinitionRegistryInterface & MockObject $breadcrumbDefinitionRegistry;

    private ContextResolver & MockObject $contextResolver;

    private BreadcrumbCreator & MockObject $breadcrumbCreator;

    private CurrentBreadcrumbsResolver $currentBreadcrumbsResolver;

    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    /**
     * @var array<string, BreadcrumbDefinition>
     */
    private array $definitions = [];

    /**
     * @var array<string, Breadcrumb>
     */
    private array $breadcrumbsByRouteName = [];

    private CurrentBreadcrumbs $result;

    protected function setUp(): void
    {
        $this->currentRouteNameResolver     = $this->createMock(CurrentRouteNameResolver::class);
        $this->breadcrumbDefinitionRegistry = $this->createMock(BreadcrumbDefinitionRegistryInterface::class);
        $this->contextResolver              = $this->createMock(ContextResolver::class);
        $this->breadcrumbCreator            = $this->createMock(BreadcrumbCreator::class);

        $this->currentBreadcrumbsResolver = new CurrentBreadcrumbsResolver(
            $this->currentRouteNameResolver,
            $this->breadcrumbDefinitionRegistry,
            $this->contextResolver,
            $this->breadcrumbCreator,
        );
    }

    public function testResolveReturnsEmptyWhenRouteHasNoDefinition(): void
    {
        $this->givenCurrentRoute('route_unknown');
        $this->givenContext([]);

        $this->whenResolve();

        $this->thenBreadcrumbs([]);
    }

    public function testResolveReturnsSingleBreadcrumbWhenNoParent(): void
    {
        $this->givenCurrentRoute('route_home');
        $this->givenContext([]);
        $this->givenDefinition('route_home', null);

        $this->whenResolve();

        $this->thenBreadcrumbs([$this->breadcrumbsByRouteName['route_home']]);
    }

    public function testResolveReturnsChainRootFirst(): void
    {
        $this->givenCurrentRoute('route_child');
        $this->givenContext([]);
        $this->givenDefinition('route_child', 'route_parent');
        $this->givenDefinition('route_parent', null);

        $this->whenResolve();

        $this->thenBreadcrumbs([
            $this->breadcrumbsByRouteName['route_parent'],
            $this->breadcrumbsByRouteName['route_child'],
        ]);
    }

    public function testResolveThrowsOnCircularReference(): void
    {
        $this->givenCurrentRoute('route_a');
        $this->givenContext(['key' => 'value']);
        $this->givenDefinition('route_a', 'route_b');
        $this->givenDefinition('route_b', 'route_a');

        $this->expectException(BreadcrumbCircularReferenceException::class);

        try {
            $this->whenResolve();
        } catch (BreadcrumbCircularReferenceException $e) {
            self::assertSame('route_a', $e->getRouteName());

            throw $e;
        }
    }

    /**
     * @param array<string, mixed> $context
     */
    private function givenContext(array $context): void
    {
        $this->context = $context;
    }

    private function givenCurrentRoute(string $routeName): void
    {
        $this->currentRouteNameResolver
            ->method('resolve')
            ->willReturn($routeName)
        ;
    }

    private function givenDefinition(string $routeName, ?string $parentRouteName): void
    {
        $parentDefinition = $parentRouteName !== null
            ? new ParentBreadcrumbDefinition($parentRouteName, StringMap::createEmpty())
            : null;

        $this->definitions[$routeName] = new BreadcrumbDefinition(
            routeName:                  $routeName,
            label:                      $routeName,
            parameters:                 StringMap::createEmpty(),
            parentBreadcrumbDefinition: $parentDefinition,
        );

        $this->breadcrumbsByRouteName[$routeName] = new Breadcrumb(
            label:           $routeName,
            routeName:       $routeName,
            routeParameters: [],
        );
    }

    private function whenResolve(): void
    {
        $this->contextResolver
            ->method('resolve')
            ->willReturnArgument(0)
        ;

        $this->breadcrumbDefinitionRegistry
            ->method('tryGet')
            ->willReturnCallback(
                fn(string $name) => $this->definitions[$name] ?? null,
            )
        ;

        $this->breadcrumbCreator
            ->method('create')
            ->willReturnCallback(
                fn(BreadcrumbDefinition $definition) => $this->breadcrumbsByRouteName[$definition->getRouteName()],
            )
        ;

        $this->result = $this->currentBreadcrumbsResolver->resolve($this->context);
    }

    /**
     * @param Breadcrumb[] $expected
     */
    private function thenBreadcrumbs(array $expected): void
    {
        $breadcrumbs = iterator_to_array($this->result->getBreadcrumbs());

        self::assertCount(count($expected), $breadcrumbs);

        foreach ($expected as $index => $breadcrumb) {
            self::assertSame($breadcrumb, $breadcrumbs[$index]);
        }
    }
}
