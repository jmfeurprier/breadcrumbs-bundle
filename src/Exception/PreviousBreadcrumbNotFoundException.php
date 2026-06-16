<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

class PreviousBreadcrumbNotFoundException extends PreviousBreadcrumbResolutionException
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        private readonly array $context,
    ) {
        parent::__construct(
            message: sprintf(
                         'Previous breadcrumb not found. Available context entries: %s.',
                         implode(
                             ', ',
                             array_keys($this->context),
                         ),
                     ),
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
