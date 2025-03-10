<?php

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Exception\TemplateRenderingException;

interface CurrentBreadcrumbsFetcherInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @throws TemplateRenderingException
     */
    public function fetch(array $context): CurrentBreadcrumbs;
}
