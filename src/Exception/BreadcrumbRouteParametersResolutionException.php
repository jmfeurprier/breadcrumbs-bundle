<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

use Throwable;

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
            message:  $this->buildMessage(),
            previous: $previous,
        );
    }

    private function buildMessage(): string
    {
        return sprintf(
            'Failed resolving breadcrumb route parameters (route: %s, label: %s). Available context entries: %s.',
            $this->routeName,
            $this->label,
            implode(
                ', ',
                array_keys($this->context),
            ),
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
