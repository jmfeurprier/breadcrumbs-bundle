<?php

namespace Jmf\Breadcrumbs\TemplateRendering;

use Jmf\Breadcrumbs\Exception\TemplateRenderingException;
use Throwable;
use Twig\Environment as TwigEnvironment;

readonly class TemplateRenderer
{
    public function __construct(
        private TwigEnvironment $twigEnvironment,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws TemplateRenderingException
     */
    public function renderFromString(
        string $template,
        array $context = []
    ): string {
        try {
            return $this->twigEnvironment->createTemplate($template)->render($context);
        } catch (Throwable $e) {
            throw new TemplateRenderingException($template, $context, $e);
        }
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws TemplateRenderingException
     */
    public function renderFromFile(
        string $name,
        array $context = []
    ): string {
        try {
            return $this->twigEnvironment->render($name, $context);
        } catch (Throwable $e) {
            throw new TemplateRenderingException($name, $context, $e);
        }
    }
}
