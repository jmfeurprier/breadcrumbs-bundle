<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

enum CurrentBreadcrumbNotFoundBehavior: string
{
    case FAIL = 'fail';
    case HIDE = 'hide';
}
