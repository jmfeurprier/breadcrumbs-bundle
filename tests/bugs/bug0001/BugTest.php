<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\bugs\bug0001;

use Jmf\Breadcrumbs\Compilation\BreadcrumbDefinitionCompiler;
use Jmf\Breadcrumbs\Compilation\BreadcrumbDefinitionCollectionCompiler;
use Jmf\Breadcrumbs\Compilation\ParentBreadcrumbDefinitionCompiler;
use Jmf\Breadcrumbs\Model\Breadcrumb;
use Jmf\Breadcrumbs\Model\CurrentBreadcrumbs;
use Jmf\Breadcrumbs\Registry\BreadcrumbDefinitionRegistryFactory;
use Jmf\Breadcrumbs\Registry\BreadcrumbDefinitionRegistryInterface;
use Jmf\Breadcrumbs\Rendering\BreadcrumbLabelRenderer;
use Jmf\Breadcrumbs\Resolution\BreadcrumbCreator;
use Jmf\Breadcrumbs\Resolution\BreadcrumbRouteParametersResolver;
use Jmf\Breadcrumbs\Resolution\ContextResolver;
use Jmf\Breadcrumbs\Resolution\CurrentBreadcrumbsResolver;
use Jmf\Breadcrumbs\Routing\CurrentRouteNameResolver;
use Jmf\Breadcrumbs\Tests\bugs\bug0001\fixtures\Cost;
use Jmf\Breadcrumbs\Tests\bugs\bug0001\fixtures\Project;
use Jmf\Breadcrumbs\Tests\bugs\bug0001\fixtures\Task;
use Jmf\TemplateRendering\TemplateRenderer;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Yaml\Parser;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class BugTest extends TestCase
{
    private RequestStack $requestStack;

    private CurrentBreadcrumbsResolver $currentBreadcrumbsFetcher;

    private CurrentBreadcrumbs $result;

    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    #[Override]
    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();

        $this->currentBreadcrumbsFetcher = new CurrentBreadcrumbsResolver(
            new CurrentRouteNameResolver(
                $this->requestStack,
            ),
            $this->getBreadcrumbConfigurationRegistry(),
            new ContextResolver(
                new PropertyAccessor(),
            ),
            new BreadcrumbCreator(
                new BreadcrumbLabelRenderer(
                    $this->getTemplateRenderer(),
                ),
                new BreadcrumbRouteParametersResolver(
                    new PropertyAccessor(),
                ),
            ),
        );
    }

    private function getTemplateRenderer(): TemplateRendererInterface
    {
        return new TemplateRenderer(
            new Environment(
                new ArrayLoader(),
            ),
        );
    }

    private function getBreadcrumbConfigurationRegistry(): BreadcrumbDefinitionRegistryInterface
    {
        /**
         * @var array{
         *     parameters: array{
         *         breadcrumbs: array<non-empty-string, mixed>
         *     }
         * } $config
         */
        $config = (new Parser())->parseFile(__DIR__ . '/fixtures/breadcrumbs.yaml');

        $breadcrumbDefinitionRegistryFactory = new BreadcrumbDefinitionRegistryFactory(
            new BreadcrumbDefinitionCollectionCompiler(
                new BreadcrumbDefinitionCompiler(
                    new ParentBreadcrumbDefinitionCompiler(),
                ),
            ),
            $config['parameters']['breadcrumbs'],
        );

        return $breadcrumbDefinitionRegistryFactory->create();
    }

    public function testBug(): void
    {
        $this->givenCurrentRoute('cost.create');

        $project = new Project('project-id', 'Project Name');
        $task    = new Task($project, 'task-id', 'Task Name');
        $cost    = new Cost($task, 'cost-id');

        $this->givenContext(
            [
                'cost' => $cost,
            ],
        );

        $this->whenFetch();

        $this->thenBreadcrumbs(
            [
                'Dashboard'              => 'dashboard',
                'Projects'               => 'project.index',
                'Project "Project Name"' => 'project.read',
                'Task "Task Name"'       => 'task.read',
                'Cost - Create'          => 'cost.create',
            ],
        );
    }

    private function givenCurrentRoute(string $route): void
    {
        $request = new Request(
            attributes: [
                            '_route' => $route,
                        ],
        );

        $this->requestStack->push($request);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function givenContext(array $context): void
    {
        $this->context = $context;
    }

    private function whenFetch(): void
    {
        $this->result = $this->currentBreadcrumbsFetcher->resolve($this->context);
    }

    /**
     * @param array<string, string> $expected
     */
    private function thenBreadcrumbs(array $expected): void
    {
        $breadcrumbs = iterator_to_array($this->result->getBreadcrumbs());

        $this->assertCount(count($expected), $breadcrumbs);

        foreach ($expected as $label => $routeName) {
            $breadcrumb = array_shift($breadcrumbs);

            $this->assertInstanceOf(Breadcrumb::class, $breadcrumb);
            $this->assertSame($label, $breadcrumb->getLabel());
            $this->assertSame($routeName, $breadcrumb->getRouteName());
        }
    }
}
