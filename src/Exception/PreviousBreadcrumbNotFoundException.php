<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

class PreviousBreadcrumbNotFoundException extends PreviousBreadcrumbResolutionException
{
    public function __construct()
    {
        parent::__construct(
            message: 'Previous breadcrumb not found.',
        );
    }
}
