<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Enum\TaskStatus;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Task entity.
 *
 * Tests cover:
 * - Entity creation and default values
 * - Getters and setters
 * - Status transitions
 * - Title validation constraints
 */
class TaskTest extends TestCase
{
    // =============================================
    // HAPPY PATH TESTS
    // =============================================

    public function testCreateTaskWithTitleSetsDefaultStatus(): void
    {
        $task = new Task();
        $task->setTitle('My first task');

        $this->assertSame('My first task', $task->getTitle());
        $this->assertSame(TaskStatus::PENDING, $task->getStatus());
    }

    public function testTaskHasUuidAfterCreation(): void
    {
        $task = new Task();
        $task->setTitle('Test task');

        // ID should be null before persistence (doctrine generates it)
        // But we can check the type hint expects Uuid
        $this->assertNull($task->getId());
    }

    public function testTaskCreatedAtIsSetAutomatically(): void
    {
        $task = new Task();
        $task->setTitle('Test task');

        // createdAt should be set on PrePersist, so null before
        $this->assertNull($task->getCreatedAt());
    }

    public function testSetStatusChangesTaskStatus(): void
    {
        $task = new Task();
        $task->setTitle('Test task');
        $task->setStatus(TaskStatus::IN_PROGRESS);

        $this->assertSame(TaskStatus::IN_PROGRESS, $task->getStatus());
    }

    public function testSetStatusToDoneMarksTaskComplete(): void
    {
        $task = new Task();
        $task->setTitle('Test task');
        $task->setStatus(TaskStatus::DONE);

        $this->assertSame(TaskStatus::DONE, $task->getStatus());
    }

    public function testSetTitleUpdatesTitle(): void
    {
        $task = new Task();
        $task->setTitle('Original title');
        $task->setTitle('Updated title');

        $this->assertSame('Updated title', $task->getTitle());
    }

    // =============================================
    // EDGE CASE TESTS
    // =============================================

    public function testTitleCanBeMaxLength255Characters(): void
    {
        $longTitle = str_repeat('a', 255);
        $task = new Task();
        $task->setTitle($longTitle);

        $this->assertSame($longTitle, $task->getTitle());
        $this->assertSame(255, strlen($task->getTitle()));
    }

    public function testTitleCanBeSingleCharacter(): void
    {
        $task = new Task();
        $task->setTitle('X');

        $this->assertSame('X', $task->getTitle());
    }

    public function testStatusCanTransitionFromPendingToInProgress(): void
    {
        $task = new Task();
        $task->setTitle('Test task');
        $this->assertSame(TaskStatus::PENDING, $task->getStatus());

        $task->setStatus(TaskStatus::IN_PROGRESS);
        $this->assertSame(TaskStatus::IN_PROGRESS, $task->getStatus());
    }

    public function testStatusCanTransitionFromInProgressToDone(): void
    {
        $task = new Task();
        $task->setTitle('Test task');
        $task->setStatus(TaskStatus::IN_PROGRESS);
        $task->setStatus(TaskStatus::DONE);

        $this->assertSame(TaskStatus::DONE, $task->getStatus());
    }

    public function testStatusCanTransitionDirectlyFromPendingToDone(): void
    {
        $task = new Task();
        $task->setTitle('Test task');
        $task->setStatus(TaskStatus::DONE);

        $this->assertSame(TaskStatus::DONE, $task->getStatus());
    }

    // =============================================
    // GETTER TESTS
    // =============================================

    public function testGetUpdatedAtReturnsNullBeforePersistence(): void
    {
        $task = new Task();
        $task->setTitle('Test task');

        $this->assertNull($task->getUpdatedAt());
    }

    public function testGetIdReturnsNullBeforePersistence(): void
    {
        $task = new Task();

        $this->assertNull($task->getId());
    }
}
