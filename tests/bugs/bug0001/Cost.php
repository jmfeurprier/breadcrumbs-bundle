<?php

namespace Jmf\Breadcrumbs\Tests\bugs\bug0001;

readonly class Cost
{
    public function __construct(
        private Task $task,
        private string $id,
    ) {
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
