<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Compilation;

use Jmf\Breadcrumbs\Compilation\BreadcrumbDefinitionCompiler;
use Jmf\Breadcrumbs\Compilation\ParentBreadcrumbDefinitionCompiler;
use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Exception\MissingBreadcrumbLabelException;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class BreadcrumbDefinitionCompilerTest extends TestCase
{
    private BreadcrumbDefinitionCompiler $compiler;

    /**
     * @var array<string, mixed>
     */
    private array $config = [];

    private BreadcrumbDefinition $result;

    protected function setUp(): void
    {
        $this->compiler = new BreadcrumbDefinitionCompiler(
            new ParentBreadcrumbDefinitionCompiler(),
        );
    }

    public function testCompileCreatesDefinitionWithLabel(): void
    {
        $this->givenConfig(['label' => 'Home']);

        $this->whenCompile('app_home');

        $this->thenResultHasRouteName('app_home');
        $this->thenResultHasLabel('Home');
        $this->thenResultHasNoParameters();
        $this->thenResultHasNoParent();
    }

    public function testCompileCreatesDefinitionWithParameters(): void
    {
        $this->givenConfig([
            'label'      => 'Item',
            'parameters' => ['id' => 'item.id'],
        ]);

        $this->whenCompile('app_item');

        $this->thenResultHasParameters(['id' => 'item.id']);
    }

    public function testCompileCreatesDefinitionWithParent(): void
    {
        $this->givenConfig([
            'label'  => 'Item',
            'parent' => ['route' => 'app_home'],
        ]);

        $this->whenCompile('app_item');

        $this->thenResultHasParentWithRouteName('app_home');
    }

    public function testCompileThrowsWhenLabelIsMissing(): void
    {
        $this->givenConfig([]);

        $this->expectException(MissingBreadcrumbLabelException::class);

        $this->whenCompile('app_home');
    }

    public function testCompileThrowsWhenLabelIsEmpty(): void
    {
        $this->givenConfig(['label' => '']);

        $this->expectException(InvalidArgumentException::class);

        $this->whenCompile('app_home');
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

    private function thenResultHasRouteName(string $routeName): void
    {
        self::assertSame($routeName, $this->result->getRouteName());
    }

    private function thenResultHasLabel(string $label): void
    {
        self::assertSame($label, $this->result->getLabel());
    }

    private function thenResultHasNoParameters(): void
    {
        self::assertSame([], $this->result->getParameters()->all());
    }

    private function thenResultHasNoParent(): void
    {
        self::assertNull($this->result->getParentBreadcrumbDefinition());
    }

    /**
     * @param array<string, string> $parameters
     */
    private function thenResultHasParameters(array $parameters): void
    {
        self::assertSame($parameters, $this->result->getParameters()->all());
    }

    private function thenResultHasParentWithRouteName(string $routeName): void
    {
        $parent = $this->result->getParentBreadcrumbDefinition();

        self::assertNotNull($parent);
        self::assertSame($routeName, $parent->getRouteName());
    }
}
