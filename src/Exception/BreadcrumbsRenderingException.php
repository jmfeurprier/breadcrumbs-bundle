<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

use Throwable;

final class BreadcrumbsRenderingException extends BreadcrumbsRuntimeException
{
    public function __construct(
        private readonly string $templatePath,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message:  sprintf("Failed rendering breadcrumbs template '%s'.", $this->templatePath),
            previous: $previous,
        );
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }
}
