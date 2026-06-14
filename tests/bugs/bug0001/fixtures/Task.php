<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\bugs\bug0001\fixtures;

readonly class Task
{
    public function __construct(
        private Project $project,
        private string $id,
        private string $name,
    ) {
    }

    public function getProject(): Project
    {
        return $this->project;
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
