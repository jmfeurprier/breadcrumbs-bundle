<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

use Throwable;

class PreviousBreadcrumbResolutionFailedException extends PreviousBreadcrumbResolutionException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(
            message:  'Failed resolving previous Breadcrumb.',
            previous: $previous,
        );
    }
}
