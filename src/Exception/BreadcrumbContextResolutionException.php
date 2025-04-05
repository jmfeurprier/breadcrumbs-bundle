<?php

namespace Jmf\Breadcrumbs\Exception;

use Throwable;

class BreadcrumbContextResolutionException extends BreadcrumbsException
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        private readonly string $key,
        private readonly string $value,
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
        return "Failed resolving parameter '{$this->key}': failed reading value '{$this->value}'. " .
            "Available context entries: " . implode(', ', array_keys($this->context)) . ".";
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
