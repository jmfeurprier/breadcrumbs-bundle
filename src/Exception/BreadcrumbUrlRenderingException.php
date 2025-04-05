<?php

namespace Jmf\Breadcrumbs\Exception;

use Throwable;

class BreadcrumbUrlRenderingException extends BreadcrumbsException
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
        return "Failed rendering breadcrumb URL (route: {$this->routeName}, label: {$this->label}). " .
            "Available context entries: " . implode(', ', array_keys($this->context)) . ".";
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
