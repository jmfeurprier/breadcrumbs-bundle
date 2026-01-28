<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Twig;

use Jmf\Breadcrumbs\Breadcrumbs\CurrentBreadcrumbs;
use Jmf\Breadcrumbs\Breadcrumbs\CurrentBreadcrumbsFetcher;
use Jmf\Breadcrumbs\Exception\BreadcrumbsException;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BreadcrumbsExtension extends AbstractExtension
{
    public final const string PREFIX_DEFAULT = '';

    public function __construct(
        private readonly CurrentBreadcrumbsFetcher $currentBreadcrumbsFetcher,
        private readonly TemplateRendererInterface $templateRenderer,
        private readonly string $templatePath,
        private readonly string $prefix = self::PREFIX_DEFAULT,
    ) {
    }

    #[Override]
    public function getFunctions(): iterable
    {
        return [
            new TwigFunction(
                $this->prefix . 'breadcrumbs_render',
                $this->render(...),
                [
                    'is_safe'       => ['html'],
                    'needs_context' => true,
                ],
            ),
            new TwigFunction(
                $this->prefix . 'breadcrumbs_get',
                $this->get(...),
                [
                    'is_safe'       => ['html'],
                    'needs_context' => true,
                ],
            ),
        ];
    }

    /**
     * @param array<string, mixed> $context
     * @param array<string, mixed> $templateParameters
     *
     * @throws BreadcrumbsException
     * @throws TemplateRenderingException
     */
    public function render(
        array $context,
        array $templateParameters = [],
    ): string {
        return $this->templateRenderer->renderFromFile(
            $this->templatePath,
            $templateParameters + [
                'breadcrumbs' => $this->get($context)->getBreadcrumbs(),
            ],
        );
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws BreadcrumbsException
     */
    public function get(
        array $context,
    ): CurrentBreadcrumbs {
        return $this->currentBreadcrumbsFetcher->fetch(
            $context,
        );
    }
}
