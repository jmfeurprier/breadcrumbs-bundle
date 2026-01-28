<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\bugs\bug0001;

readonly class Project
{
    public function __construct(
        private string $id,
        private string $name,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
