<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

class CurrentBreadcrumbNotFoundException extends BreadcrumbsRuntimeException
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        private readonly array $context,
    ) {
        parent::__construct(
            message: sprintf(
                         'Breadcrumb not found for the currently requested route. Available context entries: %s.',
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
