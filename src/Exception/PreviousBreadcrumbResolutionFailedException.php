<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

use Throwable;

class PreviousBreadcrumbResolutionFailedException extends PreviousBreadcrumbResolutionException
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        private readonly array $context,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message:  sprintf(
                          'Failed resolving previous breadcrumb. Available context entries: %s.',
                          implode(
                              ', ',
                              array_keys($this->context),
                          ),
                      ),
            previous: $previous,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
