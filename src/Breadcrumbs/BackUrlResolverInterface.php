<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Exception\BackUrlResolutionException;

interface BackUrlResolverInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @throws BackUrlResolutionException
     */
    public function resolve(array $context): string;
}
