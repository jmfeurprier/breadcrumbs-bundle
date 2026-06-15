<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

use Exception;
use Throwable;

abstract class BreadcrumbsException extends Exception
{
    protected function __construct(
        string $message,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message:  $message,
            code:     $previous?->getCode() ?? 0,
            previous: $previous,
        );
    }
}
