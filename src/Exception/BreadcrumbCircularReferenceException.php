<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

class BreadcrumbCircularReferenceException extends BreadcrumbConfigurationException
{
    /**
     * @param non-empty-string     $routeName
     * @param array<string, mixed> $context
     */
    public function __construct(
        private readonly string $routeName,
        private readonly array $context,
    ) {
        parent::__construct(
            message: sprintf(
                "Circular parent reference detected for breadcrumb route '%s'. Available context entries: %s.",
                $this->routeName,
                implode(', ', array_keys($this->context)),
            ),
        );
    }

    /**
     * @return non-empty-string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
