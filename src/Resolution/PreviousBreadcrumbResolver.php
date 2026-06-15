<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbResolutionFailedException;
use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbNotFoundException;
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
        try {
            $currentBreadcrumbs = $this->currentBreadcrumbsResolver->resolve($context);
        } catch (Throwable $e) {
            throw new PreviousBreadcrumbResolutionFailedException(previous: $e);
        }

        $previousBreadcrumb = $currentBreadcrumbs->tryGetPreviousBreadcrumb();

        if ($previousBreadcrumb instanceof Breadcrumb) {
            return $previousBreadcrumb;
        }

        throw new PreviousBreadcrumbNotFoundException();
    }
}
