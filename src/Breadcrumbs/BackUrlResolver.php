<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Breadcrumbs;

use Jmf\Breadcrumbs\Exception\BackUrlResolutionException;
use Override;
use Throwable;

readonly class BackUrlResolver implements BackUrlResolverInterface
{
    public function __construct(
        private CurrentBreadcrumbsFetcherInterface $currentBreadcrumbsFetcher,
    ) {
    }

    #[Override]
    public function resolve(array $context): string
    {
        try {
            $currentBreadcrumbs = $this->currentBreadcrumbsFetcher->fetch($context);
        } catch (Throwable $e) {
            throw new BackUrlResolutionException(
                message:  'Failed resolving back URL: failed fetching current Breadcrumbs.',
                previous: $e,
            );
        }

        $previousBreadcrumb = $currentBreadcrumbs->tryGetPreviousBreadcrumb();

        if ($previousBreadcrumb instanceof Breadcrumb) {
            return $previousBreadcrumb->getPath();
        }

        throw new BackUrlResolutionException(
            message: 'Failed resolving back URL: no previous Breadcrumb.',
        );
    }
}
