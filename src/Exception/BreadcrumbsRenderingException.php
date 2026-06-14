<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

use Throwable;

final class BreadcrumbsRenderingException extends BreadcrumbsException
{
    public function __construct(
        string $templatePath,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message:  sprintf("Failed rendering breadcrumbs template '%s'.", $templatePath),
            previous: $previous,
        );
    }
}
