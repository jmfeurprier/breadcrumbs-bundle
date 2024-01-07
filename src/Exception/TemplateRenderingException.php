<?php

namespace Jmf\Breadcrumbs\Exception;

use Exception;
use Throwable;

class TemplateRenderingException extends Exception
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        private readonly string $template,
        private readonly array $context,
        ?Throwable $previous = null,
    ) {
        parent::__construct('Failed rendering template.', 0, $previous);
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
