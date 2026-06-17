<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Compilation;

use Jmf\Breadcrumbs\Compilation\ParentBreadcrumbDefinitionCompiler;
use Jmf\Breadcrumbs\Definition\ParentBreadcrumbDefinition;
use Jmf\Breadcrumbs\Exception\MissingBreadcrumbParentRouteException;
use PHPUnit\Framework\TestCase;

final class ParentBreadcrumbDefinitionCompilerTest extends TestCase
{
    private ParentBreadcrumbDefinitionCompiler $compiler;

    /**
     * @var array<string, mixed>
     */
    private array $config = [];

    private ?ParentBreadcrumbDefinition $result = null;

    protected function setUp(): void
    {
        $this->compiler = new ParentBreadcrumbDefinitionCompiler();
    }

    public function testCompileReturnsNullWhenNoParentKey(): void
    {
        $this->givenConfig(['label' => 'Home']);

        $this->whenCompile('app_home');

        $this->thenResultIsNull();
    }

    public function testCompileReturnsParentDefinition(): void
    {
        $this->givenConfig(['parent' => ['route' => 'app_home']]);

        $this->whenCompile('app_item');

        $this->thenResultHasRouteName('app_home');
        $this->thenResultHasNoParameters();
    }

    public function testCompileReturnsParentDefinitionWithParameters(): void
    {
        $this->givenConfig([
            'parent' => [
                'route'      => 'app_home',
                'parameters' => ['id' => 'item.id'],
            ],
        ]);

        $this->whenCompile('app_item');

        $this->thenResultHasParameters(['id' => 'item.id']);
    }

    public function testCompileThrowsWhenParentRouteIsMissing(): void
    {
        $this->givenConfig(['parent' => []]);

        $this->expectException(MissingBreadcrumbParentRouteException::class);

        $this->whenCompile('app_item');
    }

    /**
     * @param array<string, mixed> $config
     */
    private function givenConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @param non-empty-string $routeName
     */
    private function whenCompile(string $routeName): void
    {
        $this->result = $this->compiler->compile($routeName, $this->config);
    }

    private function thenResultIsNull(): void
    {
        self::assertNull($this->result);
    }

    private function thenResultHasRouteName(string $routeName): void
    {
        self::assertNotNull($this->result);
        self::assertSame($routeName, $this->result->getRouteName());
    }

    private function thenResultHasNoParameters(): void
    {
        self::assertNotNull($this->result);
        self::assertSame([], $this->result->getParameters()->all());
    }

    /**
     * @param array<string, string> $parameters
     */
    private function thenResultHasParameters(array $parameters): void
    {
        self::assertNotNull($this->result);
        self::assertSame($parameters, $this->result->getParameters()->all());
    }
}
