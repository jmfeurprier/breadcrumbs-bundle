<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbResolutionException;
use Override;
use Throwable;

readonly class PreviousBreadcrumbResolver implements PreviousBreadcrumbResolverInterface
{
    public function __construct(
        private CurrentBreadcrumbsFetcherInterface $currentBreadcrumbsFetcher,
    ) {
    }

    #[Override]
    public function resolve(array $context): Breadcrumb
    {
        try {
            $currentBreadcrumbs = $this->currentBreadcrumbsFetcher->fetch($context);
        } catch (Throwable $e) {
            throw new PreviousBreadcrumbResolutionException(
                message:  'Failed resolving back URL: failed fetching current Breadcrumbs.',
                previous: $e,
            );
        }

        $previousBreadcrumb = $currentBreadcrumbs->tryGetPreviousBreadcrumb();

        if ($previousBreadcrumb instanceof Breadcrumb) {
            return $previousBreadcrumb;
        }

        throw new PreviousBreadcrumbResolutionException(
            message: 'Failed resolving previous Breadcrumb.',
        );
    }
}
