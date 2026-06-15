<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

use Throwable;
use function array_keys;
use function implode;
use function sprintf;

class BreadcrumbRouteParametersResolutionException extends BreadcrumbsRuntimeException
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        private readonly string $routeName,
        private readonly string $label,
        private readonly array $context,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message:  sprintf(
                          'Failed resolving breadcrumb route parameters (route: %s, label: %s). Available context entries: %s.',
                          $this->routeName,
                          $this->label,
                          implode(
                              ', ',
                              array_keys($this->context),
                          ),
                      ),
            previous: $previous,
        );
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
