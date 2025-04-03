<?php

namespace Jmf\Breadcrumbs\TemplateRendering;

use Jmf\Breadcrumbs\Exception\TemplateRenderingException;

interface TemplateRendererInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @throws TemplateRenderingException
     */
    public function renderFromString(
        string $template,
        array $context = [],
    ): string;

    /**
     * @param array<string, mixed> $context
     *
     * @throws TemplateRenderingException
     */
    public function renderFromFile(
        string $name,
        array $context = [],
    ): string;
}
