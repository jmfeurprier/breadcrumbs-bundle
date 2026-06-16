<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Twig;

use Jmf\Breadcrumbs\Exception\BreadcrumbsRenderingException;
use Jmf\Breadcrumbs\Model\Breadcrumb;
use Jmf\Breadcrumbs\Model\BreadcrumbCollection;
use Jmf\Breadcrumbs\Resolution\CurrentBreadcrumbsResolverInterface;
use Jmf\Breadcrumbs\Twig\BreadcrumbsExtension;
use Jmf\TemplateRendering\Exception\FileTemplateRenderingException;
use Jmf\TemplateRendering\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

final class BreadcrumbsExtensionTest extends TestCase
{
    private const string TEMPLATE_PATH = 'breadcrumbs.html.twig';

    private CurrentBreadcrumbsResolverInterface & Stub $resolver;

    private TemplateRendererInterface & Stub $templateRenderer;

    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    private BreadcrumbCollection $resolvedCollection;

    protected function setUp(): void
    {
        $this->resolver         = $this->createStub(CurrentBreadcrumbsResolverInterface::class);
        $this->templateRenderer = $this->createStub(TemplateRendererInterface::class);
    }

    public function testGetReturnsBreadcrumbCollection(): void
    {
        $this->givenContext(['key' => 'value']);
        $this->givenResolvedBreadcrumbs([
                                            new Breadcrumb(label: 'Home', routeName: 'home', routeParameters: []),
                                        ]);

        $breadcrumbCollection = $this->makeExtension()->get($this->context);

        self::assertSame($this->resolvedCollection, $breadcrumbCollection);
    }

    public function testRenderReturnsRenderedTemplate(): void
    {
        $breadcrumb = new Breadcrumb(label: 'Home', routeName: 'home', routeParameters: []);

        $this->givenContext(['key' => 'value']);
        $this->givenResolvedBreadcrumbs([$breadcrumb]);

        $capturedPath    = null;
        $capturedContext = null;

        $this->templateRenderer
            ->method('renderFromFile')
            ->willReturnCallback(
                function (
                    string $path,
                    array $context,
                ) use
                (
                    &
                    $capturedPath,
                    &
                    $capturedContext,
                ): string {
                    $capturedPath    = $path;
                    $capturedContext = $context;

                    return '<nav>Home</nav>';
                },
            )
        ;

        $result = $this->makeExtension()->render($this->context);

        self::assertSame('<nav>Home</nav>', $result);
        self::assertSame(self::TEMPLATE_PATH, $capturedPath);
        self::assertSame(['breadcrumbs' => [$breadcrumb]], $capturedContext);
    }

    public function testRenderMergesTemplateParametersWithBreadcrumbs(): void
    {
        $breadcrumb = new Breadcrumb(label: 'Home', routeName: 'home', routeParameters: []);

        $this->givenContext([]);
        $this->givenResolvedBreadcrumbs([$breadcrumb]);

        $capturedContext = null;

        $this->templateRenderer
            ->method('renderFromFile')
            ->willReturnCallback(
                function (
                    string $path,
                    array $context,
                ) use
                (
                    &
                    $capturedContext,
                ): string {
                    $capturedContext = $context;

                    return '';
                },
            )
        ;

        $this->makeExtension()->render($this->context, ['extra' => 'param']);

        self::assertSame(
            [
                'extra'       => 'param',
                'breadcrumbs' => [$breadcrumb],
            ],
            $capturedContext,
        );
    }

    public function testRenderWrapsTemplateRenderingException(): void
    {
        $this->givenContext([]);
        $this->givenResolvedBreadcrumbs([]);

        $this->templateRenderer
            ->method('renderFromFile')
            ->willThrowException(new FileTemplateRenderingException(self::TEMPLATE_PATH))
        ;

        $this->expectException(BreadcrumbsRenderingException::class);

        try {
            $this->makeExtension()->render($this->context);
        } catch (BreadcrumbsRenderingException $e) {
            self::assertSame(self::TEMPLATE_PATH, $e->getTemplatePath());

            throw $e;
        }
    }

    public function testGetFunctionsRegistersExpectedFunctions(): void
    {
        $functions = $this->makeExtension()->getFunctions();

        $names = array_map(
            fn(
                TwigFunction $twigFunction,
            ): string => $twigFunction->getName(),
            $functions,
        );

        self::assertContains('breadcrumbs_render', $names);
        self::assertContains('breadcrumbs_get', $names);
    }

    public function testGetFunctionsUsesPrefix(): void
    {
        $functions = $this->makeExtension(prefix: 'my_')->getFunctions();

        $names = array_map(
            fn(
                TwigFunction $twigFunction,
            ): string => $twigFunction->getName(),
            $functions,
        );

        self::assertContains('my_breadcrumbs_render', $names);
        self::assertContains('my_breadcrumbs_get', $names);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function givenContext(array $context): void
    {
        $this->context = $context;
    }

    /**
     * @param Breadcrumb[] $breadcrumbs
     */
    private function givenResolvedBreadcrumbs(array $breadcrumbs): void
    {
        $this->resolvedCollection = new BreadcrumbCollection($breadcrumbs);

        $this->resolver
            ->method('resolve')
            ->willReturn($this->resolvedCollection)
        ;
    }

    private function makeExtension(string $prefix = BreadcrumbsExtension::PREFIX_DEFAULT): BreadcrumbsExtension
    {
        return new BreadcrumbsExtension(
            currentBreadcrumbsResolver: $this->resolver,
            templateRenderer:           $this->templateRenderer,
            templatePath:               self::TEMPLATE_PATH,
            prefix:                     $prefix,
        );
    }
}
