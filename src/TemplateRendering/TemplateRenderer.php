<?php

namespace Jmf\Breadcrumbs\TemplateRendering;

use Jmf\Breadcrumbs\Exception\TemplateRenderingException;
use Override;
use Throwable;
use Twig\Environment as TwigEnvironment;

readonly class TemplateRenderer implements TemplateRendererInterface
{
    public function __construct(
        private TwigEnvironment $twigEnvironment,
    ) {
    }

    #[Override]
    public function renderFromString(
        string $template,
        array $context = [],
    ): string {
        try {
            return $this->twigEnvironment->createTemplate($template)->render($context);
        } catch (Throwable $e) {
            throw new TemplateRenderingException($template, $context, $e);
        }
    }

    #[Override]
    public function renderFromFile(
        string $name,
        array $context = [],
    ): string {
        try {
            return $this->twigEnvironment->render($name, $context);
        } catch (Throwable $e) {
            throw new TemplateRenderingException($name, $context, $e);
        }
    }
}
