<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Twig;

use Jmf\Breadcrumbs\Exception\BreadcrumbsException;
use Jmf\Breadcrumbs\Exception\BreadcrumbsRenderingException;
use Jmf\Breadcrumbs\Model\CurrentBreadcrumbs;
use Jmf\Breadcrumbs\Resolution\CurrentBreadcrumbsResolverInterface;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BreadcrumbsExtension extends AbstractExtension
{
    public final const string PREFIX_DEFAULT = '';

    public function __construct(
        private readonly CurrentBreadcrumbsResolverInterface $currentBreadcrumbsFetcher,
        private readonly TemplateRendererInterface $templateRenderer,
        private readonly string $templatePath,
        private readonly string $prefix = self::PREFIX_DEFAULT,
    ) {
    }

    #[Override]
    public function getFunctions(): array
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
     * @throws BreadcrumbsRenderingException
     */
    public function render(
        array $context,
        array $templateParameters = [],
    ): string {
        try {
            return $this->templateRenderer->renderFromFile(
                $this->templatePath,
                array_merge(
                    $templateParameters,
                    [
                        'breadcrumbs' => $this->get($context)->getBreadcrumbs(),
                    ],
                ),
            );
        } catch (TemplateRenderingException $e) {
            throw new BreadcrumbsRenderingException(
                templatePath: $this->templatePath,
                previous:     $e,
            );
        }
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws BreadcrumbsException
     */
    public function get(
        array $context,
    ): CurrentBreadcrumbs {
        return $this->currentBreadcrumbsFetcher->resolve(
            $context,
        );
    }
}
