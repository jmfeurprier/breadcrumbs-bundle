<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Resolution;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\StringMap;
use Jmf\Breadcrumbs\Exception\BreadcrumbLabelRenderingException;
use Jmf\Breadcrumbs\Exception\BreadcrumbRouteParametersResolutionException;
use Jmf\Breadcrumbs\Model\Breadcrumb;
use Jmf\Breadcrumbs\Rendering\BreadcrumbLabelRenderer;
use Jmf\Breadcrumbs\Resolution\BreadcrumbCreator;
use Jmf\Breadcrumbs\Resolution\BreadcrumbRouteParametersResolver;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class BreadcrumbCreatorTest extends TestCase
{
    private BreadcrumbCreator $breadcrumbCreator;

    private BreadcrumbLabelRenderer & Stub $labelRenderer;

    private BreadcrumbRouteParametersResolver & Stub $parametersResolver;

    private BreadcrumbDefinition $definition;

    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    private Breadcrumb $result;

    protected function setUp(): void
    {
        $this->labelRenderer      = $this->createStub(BreadcrumbLabelRenderer::class);
        $this->parametersResolver = $this->createStub(BreadcrumbRouteParametersResolver::class);

        $this->breadcrumbCreator = new BreadcrumbCreator(
            $this->labelRenderer,
            $this->parametersResolver,
        );

        $this->definition = new BreadcrumbDefinition(
            routeName:                  'app_home',
            label:                      'Home',
            parameters:                 StringMap::createEmpty(),
            parentBreadcrumbDefinition: null,
        );
    }

    public function testCreateReturnsBreadcrumb(): void
    {
        $this->givenContext(['key' => 'value']);
        $this->givenLabelRendererReturns('Home');
        $this->givenParametersResolverReturns(['id' => '42']);

        $this->whenCreate();

        $this->thenResultHasLabel('Home');
        $this->thenResultHasRouteName('app_home');
        $this->thenResultHasRouteParameters(['id' => '42']);
    }

    public function testCreatePropagatesLabelRenderingException(): void
    {
        $this->givenContext([]);
        $this->labelRenderer->method('render')->willThrowException(
            new BreadcrumbLabelRenderingException(
                routeName: 'app_home',
                label:     'Home',
                context:   [],
            ),
        );

        $this->expectException(BreadcrumbLabelRenderingException::class);

        $this->whenCreate();
    }

    public function testCreatePropagatesRouteParametersResolutionException(): void
    {
        $this->givenContext([]);
        $this->givenLabelRendererReturns('Home');
        $this->parametersResolver->method('resolve')->willThrowException(
            new BreadcrumbRouteParametersResolutionException(
                routeName: 'app_home',
                label:     'Home',
                context:   [],
            ),
        );

        $this->expectException(BreadcrumbRouteParametersResolutionException::class);

        $this->whenCreate();
    }

    /**
     * @param array<string, mixed> $context
     */
    private function givenContext(array $context): void
    {
        $this->context = $context;
    }

    private function givenLabelRendererReturns(string $label): void
    {
        $this->labelRenderer->method('render')->willReturn($label);
    }

    /**
     * @param array<string, string> $parameters
     */
    private function givenParametersResolverReturns(array $parameters): void
    {
        $this->parametersResolver->method('resolve')->willReturn($parameters);
    }

    private function whenCreate(): void
    {
        $this->result = $this->breadcrumbCreator->create($this->definition, $this->context);
    }

    private function thenResultHasLabel(string $label): void
    {
        self::assertSame($label, $this->result->getLabel());
    }

    private function thenResultHasRouteName(string $routeName): void
    {
        self::assertSame($routeName, $this->result->getRouteName());
    }

    /**
     * @param array<string, mixed> $routeParameters
     */
    private function thenResultHasRouteParameters(array $routeParameters): void
    {
        self::assertSame($routeParameters, $this->result->getRouteParameters());
    }
}
