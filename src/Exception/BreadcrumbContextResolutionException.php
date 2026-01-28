<?php

declare(strict_types=1);

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
        return sprintf(
            "Failed resolving parameter '%s': failed reading value '%s'. Available context entries: %s.",
            $this->key,
            $this->value,
            implode(
                ', ',
                array_keys($this->context),
            ),
        );
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
