<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Enum\TaskStatus;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testDefaultStatusIsPending(): void
    {
        $task = new Task();
        $this->assertSame(TaskStatus::PENDING, $task->getStatus());
    }

    public function testCanSetTitleAndStatus(): void
    {
        $task = new Task();
        $task->setTitle('Test Task');
        $task->setStatus(TaskStatus::IN_PROGRESS);

        $this->assertSame('Test Task', $task->getTitle());
        $this->assertSame(TaskStatus::IN_PROGRESS, $task->getStatus());
    }
}
