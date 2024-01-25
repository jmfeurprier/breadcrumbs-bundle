<?php

namespace Jmf\Breadcrumbs\Twig;

use Jmf\Breadcrumbs\Breadcrumbs\CurrentBreadcrumbs;
use Jmf\Breadcrumbs\Breadcrumbs\CurrentBreadcrumbsFetcher;
use Jmf\Breadcrumbs\Exception\TemplateRenderingException;
use Jmf\Breadcrumbs\TemplateRendering\TemplateRenderer;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BreadcrumbsExtension extends AbstractExtension
{
    public final const string PREFIX_DEFAULT = 'jmf_';

    /**
     * @var array<string, string>
     */
    private const array FUNCTIONS = [
        'breadcrumbs_render' => 'render',
        'breadcrumbs_get'    => 'get',
    ];

    public function __construct(
        private readonly CurrentBreadcrumbsFetcher $currentBreadcrumbsFetcher,
        private readonly TemplateRenderer $templateRenderer,
        private readonly string $templatePath,
        private readonly string $prefix = self::PREFIX_DEFAULT,
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    #[Override]
    public function getFunctions(): iterable
    {
        $functions = [];

        foreach (self::FUNCTIONS as $function => $method) {
            $functions[] = new TwigFunction(
                ($this->prefix . $function),
                [
                    $this,
                    $method,
                ],
                [
                    'is_safe'       => ['html'],
                    'needs_context' => true,
                ]
            );
        }

        return $functions;
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @throws TemplateRenderingException
     */
    public function render(
        array $context,
        array $parameters = [],
    ): string {
        return $this->templateRenderer->renderFromFile(
            $this->templatePath,
            $parameters + [
                'breadcrumbs' => $this->get($context)->getBreadcrumbs(),
            ]
        );
    }

    /**
     * @throws TemplateRenderingException
     */
    public function get(
        array $context,
    ): CurrentBreadcrumbs {
        return $this->currentBreadcrumbsFetcher->fetch(
            $context
        );
    }
}
