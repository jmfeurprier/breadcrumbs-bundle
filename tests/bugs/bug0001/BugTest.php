<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\bugs\bug0001;

use Jmf\Breadcrumbs\Breadcrumbs\Breadcrumb;
use Jmf\Breadcrumbs\Breadcrumbs\BreadcrumbCreator;
use Jmf\Breadcrumbs\Breadcrumbs\BreadcrumbLabelRenderer;
use Jmf\Breadcrumbs\Breadcrumbs\BreadcrumbRouteParametersResolver;
use Jmf\Breadcrumbs\Breadcrumbs\ContextResolver;
use Jmf\Breadcrumbs\Breadcrumbs\CurrentBreadcrumbs;
use Jmf\Breadcrumbs\Breadcrumbs\CurrentBreadcrumbsFetcher;
use Jmf\Breadcrumbs\Breadcrumbs\RouteNameResolver;
use Jmf\Breadcrumbs\Configuration\BreadcrumbConfigurationLoader;
use Jmf\Breadcrumbs\Configuration\BreadcrumbConfigurationRepositoryFactory;
use Jmf\Breadcrumbs\Configuration\BreadcrumbConfigurationRepositoryInterface;
use Jmf\Breadcrumbs\Configuration\BreadcrumbConfigurationsLoader;
use Jmf\Breadcrumbs\Configuration\ParentBreadcrumbConfigurationLoader;
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

    private CurrentBreadcrumbsFetcher $currentBreadcrumbsFetcher;

    private CurrentBreadcrumbs $result;

    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    #[Override]
    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();

        $this->currentBreadcrumbsFetcher = new CurrentBreadcrumbsFetcher(
            new RouteNameResolver(
                $this->requestStack,
            ),
            $this->getBreadcrumbConfigurationRepository(),
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

    private function getBreadcrumbConfigurationRepository(): BreadcrumbConfigurationRepositoryInterface
    {
        /**
         * @var array{
         *     parameters: array{
         *         breadcrumbs: array<string, mixed>
         *     }
         * } $config
         */
        $config = (new Parser())->parseFile(__DIR__ . '/breadcrumbs.yaml');

        $breadcrumbConfigurationRepositoryFactory = new BreadcrumbConfigurationRepositoryFactory(
            new BreadcrumbConfigurationsLoader(
                new BreadcrumbConfigurationLoader(
                    new ParentBreadcrumbConfigurationLoader(),
                ),
            ),
            $config['parameters']['breadcrumbs'],
        );

        return $breadcrumbConfigurationRepositoryFactory->make();
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
        $this->result = $this->currentBreadcrumbsFetcher->fetch($this->context);
    }

    /**
     * @param array<string, string> $expected
     */
    private function thenBreadcrumbs(array $expected): void
    {
        $breadcrumbs = (array) $this->result->getBreadcrumbs();

        $this->assertCount(count($expected), $breadcrumbs);

        foreach ($expected as $label => $routeName) {
            $breadcrumb = array_shift($breadcrumbs);

            $this->assertInstanceOf(Breadcrumb::class, $breadcrumb);
            $this->assertSame($label, $breadcrumb->getLabel());
            $this->assertSame($routeName, $breadcrumb->getRouteName());
        }
    }
}
