<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Rendering;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Exception\BreadcrumbLabelRenderingException;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Throwable;

readonly class BreadcrumbLabelRenderer
{
    public function __construct(
        private TemplateRendererInterface $templateRenderer,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws BreadcrumbLabelRenderingException
     */
    public function render(
        BreadcrumbDefinition $breadcrumbDefinition,
        array $context,
    ): string {
        try {
            return $this->templateRenderer->renderFromString(
                '{% autoescape false %}' . $breadcrumbDefinition->getLabel() . '{% endautoescape %}',
                $context,
            );
        } catch (Throwable $e) {
            throw new BreadcrumbLabelRenderingException(
                routeName: $breadcrumbDefinition->getRouteName(),
                label:     $breadcrumbDefinition->getLabel(),
                context:   $context,
                previous:  $e,
            );
        }
    }
}
