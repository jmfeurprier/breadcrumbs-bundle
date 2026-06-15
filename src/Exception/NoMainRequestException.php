<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

class NoMainRequestException extends BreadcrumbsRuntimeException
{
    public function __construct()
    {
        parent::__construct(
            message: 'No main request.',
        );
    }
}
