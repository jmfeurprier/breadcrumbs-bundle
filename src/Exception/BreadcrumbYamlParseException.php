<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Exception;

use Throwable;

class BreadcrumbYamlParseException extends BreadcrumbConfigurationException
{
    public function __construct(
        private readonly string $filePath,
        Throwable $previous,
    ) {
        parent::__construct(
            message:  sprintf("Failed to parse YAML breadcrumb configuration file '%s'.", $this->filePath),
            previous: $previous,
        );
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}