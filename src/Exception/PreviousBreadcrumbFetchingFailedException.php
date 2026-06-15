<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

use Throwable;

class PreviousBreadcrumbFetchingFailedException extends PreviousBreadcrumbResolutionException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(
            message:  'Failed resolving previous Breadcrumb: failed fetching current Breadcrumbs.',
            previous: $previous,
        );
    }
}
