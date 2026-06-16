<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Resolution;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\StringMap;
use Jmf\Breadcrumbs\Exception\BreadcrumbRouteParametersResolutionException;
use Jmf\Breadcrumbs\Resolution\BreadcrumbRouteParametersResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccessor;

final class BreadcrumbRouteParametersResolverTest extends TestCase
{
    private BreadcrumbRouteParametersResolver $resolver;

    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    /**
     * @var array<string, mixed>
     */
    private array $result;

    protected function setUp(): void
    {
        $this->resolver = new BreadcrumbRouteParametersResolver(new PropertyAccessor());
    }

    public function testResolveWithNoParametersReturnsEmptyArray(): void
    {
        $this->givenContext(
            [
                'entity' => new class {
                },
            ],
        );

        $breadcrumbDefinition = $this->givenDefinition('route_home', []);

        $this->whenResolve($breadcrumbDefinition);

        $this->thenResult([]);
    }

    public function testResolveMapsParametersFromContext(): void
    {
        $entity = new class {
            public function getId(): string
            {
                return '99';
            }
        };

        $this->givenContext(['entity' => $entity]);

        $breadcrumbDefinition = $this->givenDefinition('route_detail', ['id' => 'entity.id']);

        $this->whenResolve($breadcrumbDefinition);

        $this->thenResult(['id' => '99']);
    }

    public function testResolveThrowsOnFailedParameterAccess(): void
    {
        $this->givenContext(
            [
                'entity' => new class {
                },
            ],
        );

        $breadcrumbDefinition = $this->givenDefinition('route_detail', ['id' => 'entity.nonExistentProperty']);

        $this->expectException(BreadcrumbRouteParametersResolutionException::class);

        try {
            $this->whenResolve($breadcrumbDefinition);
        } catch (BreadcrumbRouteParametersResolutionException $e) {
            self::assertSame('route_detail', $e->getRouteName());

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

    /**
     * @param non-empty-string      $routeName
     * @param array<string, string> $parameters
     */
    private function givenDefinition(
        string $routeName,
        array $parameters,
    ): BreadcrumbDefinition {
        return new BreadcrumbDefinition(
            routeName:                  $routeName,
            label:                      $routeName,
            parameters:                 new StringMap($parameters),
            parentBreadcrumbDefinition: null,
        );
    }

    private function whenResolve(BreadcrumbDefinition $definition): void
    {
        $this->result = $this->resolver->resolve($definition, $this->context);
    }

    /**
     * @param array<string, mixed> $expected
     */
    private function thenResult(array $expected): void
    {
        self::assertSame($expected, $this->result);
    }
}
