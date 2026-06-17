<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Compilation;

use Jmf\Breadcrumbs\Compilation\BreadcrumbDefinitionCollectionCompiler;
use Jmf\Breadcrumbs\Compilation\BreadcrumbDefinitionCompiler;
use Jmf\Breadcrumbs\Compilation\ParentBreadcrumbDefinitionCompiler;
use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\BreadcrumbDefinitionCollection;
use PHPUnit\Framework\TestCase;

final class BreadcrumbDefinitionCollectionCompilerTest extends TestCase
{
    private BreadcrumbDefinitionCollectionCompiler $compiler;

    /**
     * @var array<non-empty-string, mixed>
     */
    private array $config = [];

    private BreadcrumbDefinitionCollection $result;

    protected function setUp(): void
    {
        $this->compiler = new BreadcrumbDefinitionCollectionCompiler(
            new BreadcrumbDefinitionCompiler(
                new ParentBreadcrumbDefinitionCompiler(),
            ),
        );
    }

    public function testCompileReturnsEmptyCollection(): void
    {
        $this->givenConfig([]);

        $this->whenCompile();

        $this->thenResultHasCount(0);
    }

    public function testCompileReturnsSingleDefinition(): void
    {
        $this->givenConfig([
                               'app_home' => ['label' => 'Home'],
                           ]);

        $this->whenCompile();

        $this->thenResultHasCount(1);
        $this->thenResultContainsRouteNames(['app_home']);
    }

    public function testCompileReturnsMultipleDefinitions(): void
    {
        $this->givenConfig([
                               'app_home' => ['label' => 'Home'],
                               'app_item' => ['label' => 'Item'],
                           ]);

        $this->whenCompile();

        $this->thenResultHasCount(2);
        $this->thenResultContainsRouteNames(
            [
                'app_home',
                'app_item',
            ],
        );
    }

    /**
     * @param array<non-empty-string, mixed> $config
     */
    private function givenConfig(array $config): void
    {
        $this->config = $config;
    }

    private function whenCompile(): void
    {
        $this->result = $this->compiler->compile($this->config);
    }

    private function thenResultHasCount(int $count): void
    {
        self::assertCount($count, $this->result->all());
    }

    /**
     * @param string[] $routeNames
     */
    private function thenResultContainsRouteNames(array $routeNames): void
    {
        $actual = array_map(
            static fn(
                BreadcrumbDefinition $definition,
            ): string => $definition->getRouteName(),
            $this->result->all(),
        );

        self::assertSame($routeNames, $actual);
    }
}
