<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbNotFoundException;
use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbResolutionFailedException;
use Jmf\Breadcrumbs\Model\Breadcrumb;
use Override;
use Throwable;

readonly class PreviousBreadcrumbResolver implements PreviousBreadcrumbResolverInterface
{
    public function __construct(
        private CurrentBreadcrumbsResolverInterface $currentBreadcrumbsResolver,
    ) {
    }

    #[Override]
    public function resolve(array $context): Breadcrumb
    {
        $previousBreadcrumb = $this->doTryResolve($context);

        return $previousBreadcrumb ?? throw new PreviousBreadcrumbNotFoundException(context: $context);
    }

    #[Override]
    public function tryResolve(array $context): ?Breadcrumb
    {
        return $this->doTryResolve($context);
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws PreviousBreadcrumbResolutionFailedException
     */
    private function doTryResolve(array $context): ?Breadcrumb
    {
        try {
            $currentBreadcrumbs = $this->currentBreadcrumbsResolver->resolve($context);
        } catch (Throwable $e) {
            throw new PreviousBreadcrumbResolutionFailedException(context: $context, previous: $e);
        }

        return $currentBreadcrumbs->tryGetPreviousBreadcrumb();
    }
}
