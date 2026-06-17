<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Twig;

use Jmf\Breadcrumbs\Exception\BreadcrumbsException;
use Jmf\Breadcrumbs\Exception\BreadcrumbsRenderingException;
use Jmf\Breadcrumbs\Exception\CurrentBreadcrumbNotFoundException;
use Jmf\Breadcrumbs\Model\BreadcrumbCollection;
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
        private readonly CurrentBreadcrumbsResolverInterface $currentBreadcrumbsResolver,
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
     * @throws CurrentBreadcrumbNotFoundException
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
                        'breadcrumbs' => $this->doGet($context)->all(),
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
     * @throws CurrentBreadcrumbNotFoundException
     */
    public function get(
        array $context,
    ): BreadcrumbCollection {
        return $this->doGet($context);
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws BreadcrumbsException
     * @throws CurrentBreadcrumbNotFoundException
     */
    private function doGet(
        array $context,
    ): BreadcrumbCollection {
        return $this->currentBreadcrumbsResolver->resolve(
            $context,
        );
    }
}
