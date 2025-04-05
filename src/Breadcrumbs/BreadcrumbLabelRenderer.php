<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Configuration\BreadcrumbConfiguration;
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
        BreadcrumbConfiguration $breadcrumbConfiguration,
        array $context,
    ): string {
        try {
            return $this->templateRenderer->renderFromString(
                '{% autoescape false %}' . $breadcrumbConfiguration->getLabel() . '{% endautoescape %}',
                $context,
            );
        } catch (Throwable $e) {
            throw new BreadcrumbLabelRenderingException(
                routeName: $breadcrumbConfiguration->getRouteName(),
                label:     $breadcrumbConfiguration->getLabel(),
                context:   $context,
                previous:  $e,
            );
        }
    }
}
