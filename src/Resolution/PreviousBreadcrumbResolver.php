<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbFetchingFailedException;
use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbNotFoundException;
use Jmf\Breadcrumbs\Model\Breadcrumb;
use Override;
use Throwable;

readonly class PreviousBreadcrumbResolver implements PreviousBreadcrumbResolverInterface
{
    public function __construct(
        private CurrentBreadcrumbsResolverInterface $currentBreadcrumbsFetcher,
    ) {
    }

    #[Override]
    public function resolve(array $context): Breadcrumb
    {
        try {
            $currentBreadcrumbs = $this->currentBreadcrumbsFetcher->resolve($context);
        } catch (Throwable $e) {
            throw new PreviousBreadcrumbFetchingFailedException(previous: $e);
        }

        $previousBreadcrumb = $currentBreadcrumbs->tryGetPreviousBreadcrumb();

        if ($previousBreadcrumb instanceof Breadcrumb) {
            return $previousBreadcrumb;
        }

        throw new PreviousBreadcrumbNotFoundException();
    }
}
