<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Definition;

use Webmozart\Assert\Assert;

// @todo Rename into StringMap
readonly class KeyStringCollection
{
    public static function createEmpty(): self
    {
        return new self([]);
    }

    /**
     * @param array<string, string> $values
     */
    public function __construct(
        private array $values,
    ) {
        Assert::isMap($this->values);
        Assert::allString($this->values);
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->values;
    }
}
