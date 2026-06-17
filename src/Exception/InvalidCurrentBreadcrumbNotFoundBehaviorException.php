<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

class InvalidCurrentBreadcrumbNotFoundBehaviorException extends BreadcrumbConfigurationException
{
    /**
     * @param string[] $validValues
     */
    public function __construct(
        private readonly string $value,
        private readonly array $validValues,
    ) {
        parent::__construct(
            message: sprintf(
                "Invalid current breadcrumb not found behavior '%s'. Valid values are: %s.",
                $this->value,
                implode(', ', array_map(static fn (string $v): string => sprintf("'%s'", $v), $this->validValues)),
            ),
        );
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string[]
     */
    public function getValidValues(): array
    {
        return $this->validValues;
    }
}