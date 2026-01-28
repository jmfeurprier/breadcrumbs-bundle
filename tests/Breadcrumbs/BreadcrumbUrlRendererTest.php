<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Breadcrumbs;

use Jmf\Breadcrumbs\Breadcrumbs\BreadcrumbUrlRenderer;
use Jmf\Breadcrumbs\Configuration\BreadcrumbConfiguration;
use Jmf\Breadcrumbs\Configuration\KeyStringCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class BreadcrumbUrlRendererTest extends TestCase
{
    private BreadcrumbUrlRenderer $breadcrumbUrlRenderer;

    private RouteCollection $routeCollection;

    private string $routeName;

    /**
     * @var array<string, string>
     */
    private array $breadcrumbParameters = [];

    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    private string $result;

    protected function setUp(): void
    {
        $this->routeCollection = new RouteCollection();

        $this->breadcrumbUrlRenderer = new BreadcrumbUrlRenderer(
            new UrlGenerator(
                $this->routeCollection,
                new RequestContext(),
            ),
            new PropertyAccessor(),
        );
    }

    public function testRenderWithoutParameter(): void
    {
        $this->givenRoute('article.index', '/articles');
        $this->givenRouteName('article.index');

        $this->whenRender();

        $this->thenResult('/articles');
    }

    public function testRenderWithParameter(): void
    {
        $this->givenRoute('article.read', '/articles/{id}');
        $this->givenBreadcrumbParameter('id', 'article.id');
        $this->givenRouteName('article.read');
        $this->givenContext(
            [
                'article' => (object) [
                    'id' => 123,
                ],
            ],
        );

        $this->whenRender();

        $this->thenResult('/articles/123');
    }

    /**
     * @param string[] $requirements
     */
    private function givenRoute(
        string $routeName,
        string $path,
        iterable $requirements = [],
    ): void {
        $this->routeCollection->add(
            $routeName,
            new Route(
                path:         $path,
                requirements: (array) $requirements,
            ),
        );
    }

    private function givenRouteName(string $routeName): void
    {
        $this->routeName = $routeName;
    }

    private function givenBreadcrumbParameter(string $key, string $value): void
    {
        $this->breadcrumbParameters[$key] = $value;
    }

    /**
     * @param array<string, mixed> $context
     */
    private function givenContext(array $context): void
    {
        $this->context = $context;
    }

    private function whenRender(): void
    {
        $breadcrumbConfiguration = new BreadcrumbConfiguration(
            routeName:                     $this->routeName,
            label:                         'label',
            parameters:                    new KeyStringCollection($this->breadcrumbParameters),
            parentBreadcrumbConfiguration: null,
        );

        $this->result = $this->breadcrumbUrlRenderer->render(
            $breadcrumbConfiguration,
            $this->context,
        );
    }

    private function thenResult(string $expected): void
    {
        self::assertSame($expected, $this->result);
    }
}
