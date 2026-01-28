<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Breadcrumbs;

use Jmf\Breadcrumbs\Breadcrumbs\BreadcrumbLabelRenderer;
use Jmf\Breadcrumbs\Configuration\BreadcrumbConfiguration;
use Jmf\Breadcrumbs\Configuration\KeyStringCollection;
use Jmf\Breadcrumbs\Exception\BreadcrumbLabelRenderingException;
use Jmf\TemplateRendering\TemplateRenderer;
use Jmf\TemplateRendering\TemplateRendererInterface;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class BreadcrumbLabelRendererTest extends TestCase
{
    private BreadcrumbLabelRenderer $breadcrumbLabelRenderer;

    private string $label;

    /**
     * @var array<string, mixed>
     */
    private array $parameters = [];

    private string $result;

    protected function setUp(): void
    {
        $this->breadcrumbLabelRenderer = new BreadcrumbLabelRenderer(
            $this->getTemplateRenderer(),
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

    public function testRenderWithParameter(): void
    {
        $this->givenLabel('Article #{{ article.id }}');
        $this->givenParameters(
            [
                'article' => [
                    'id' => 123,
                ],
            ],
        );

        $this->whenRender();

        $this->thenResult('Article #123');
    }

    public function testRenderDoesNotHtmlEscapeParameterValue(): void
    {
        $this->givenLabel('Article {{ article.title }}');
        $this->givenParameters(
            [
                'article' => [
                    'title' => 'Foo & Bar',
                ],
            ],
        );

        $this->whenRender();

        $this->thenResult('Article Foo & Bar');
    }

    private function givenLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function givenParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    private function whenRender(): void
    {
        $breadcrumbConfiguration = new BreadcrumbConfiguration(
            routeName:                     'route.name',
            label:                         $this->label,
            parameters:                    KeyStringCollection::createEmpty(),
            parentBreadcrumbConfiguration: null,
        );

        $this->result = $this->breadcrumbLabelRenderer->render(
            $breadcrumbConfiguration,
            $this->parameters,
        );
    }

    private function thenResult(string $expected): void
    {
        self::assertSame($expected, $this->result);
    }
}
