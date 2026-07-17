<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Functional tests for Task API endpoints.
 *
 * Tests all CRUD operations via HTTP:
 * - GET /api/tasks (collection)
 * - POST /api/tasks (create)
 * - GET /api/tasks/{id} (read)
 * - PATCH /api/tasks/{id} (update)
 * - DELETE /api/tasks/{id} (delete)
 */
class TaskApiTest extends ApiTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        // Clean up tasks table before each test
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('TRUNCATE TABLE task CASCADE');
    }

    // =============================================
    // GET COLLECTION TESTS
    // =============================================

    public function testGetTasksReturnsEmptyArrayWhenNoTasks(): void
    {
        $response = static::createClient()->request('GET', '/api/tasks');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['member' => []]);
    }

    public function testGetTasksReturnsAllTasks(): void
    {
        // Create test tasks
        $task1 = new Task();
        $task1->setTitle('Task 1');
        $this->entityManager->persist($task1);

        $task2 = new Task();
        $task2->setTitle('Task 2');
        $this->entityManager->persist($task2);

        $this->entityManager->flush();

        $response = static::createClient()->request('GET', '/api/tasks');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['totalItems' => 2]);
    }

    // =============================================
    // POST (CREATE) TESTS
    // =============================================

    public function testCreateTaskWithValidDataReturns201(): void
    {
        $response = static::createClient()->request('POST', '/api/tasks', [
            'json' => [
                'title' => 'New task',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            'title' => 'New task',
            'status' => 'pending',
        ]);
    }

    public function testCreateTaskWithStatusReturns201(): void
    {
        $response = static::createClient()->request('POST', '/api/tasks', [
            'json' => [
                'title' => 'Task in progress',
                'status' => 'in_progress',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'title' => 'Task in progress',
            'status' => 'in_progress',
        ]);
    }

    public function testCreateTaskWithEmptyTitleReturns422(): void
    {
        $response = static::createClient()->request('POST', '/api/tasks', [
            'json' => [
                'title' => '',
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateTaskWithWhitespaceOnlyTitleReturns422(): void
    {
        $response = static::createClient()->request('POST', '/api/tasks', [
            'json' => [
                'title' => '   ',
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateTaskWithoutTitleReturns422(): void
    {
        $response = static::createClient()->request('POST', '/api/tasks', [
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateTaskWithTitleTooLongReturns422(): void
    {
        $response = static::createClient()->request('POST', '/api/tasks', [
            'json' => [
                'title' => str_repeat('a', 256),
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    // =============================================
    // GET SINGLE TESTS
    // =============================================

    public function testGetTaskByIdReturns200(): void
    {
        $task = new Task();
        $task->setTitle('Test task');
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $id = $task->getId()->toRfc4122();

        $response = static::createClient()->request('GET', '/api/tasks/' . $id);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => 'Test task',
            'status' => 'pending',
        ]);
    }

    public function testGetTaskWithInvalidIdReturns404(): void
    {
        $response = static::createClient()->request('GET', '/api/tasks/00000000-0000-0000-0000-000000000000');

        $this->assertResponseStatusCodeSame(404);
    }

    // =============================================
    // PATCH (UPDATE) TESTS
    // =============================================

    public function testUpdateTaskTitleReturns200(): void
    {
        $task = new Task();
        $task->setTitle('Original title');
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $id = $task->getId()->toRfc4122();

        $response = static::createClient()->request('PATCH', '/api/tasks/' . $id, [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'title' => 'Updated title',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => 'Updated title',
        ]);
    }

    public function testUpdateTaskStatusReturns200(): void
    {
        $task = new Task();
        $task->setTitle('Task to update');
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $id = $task->getId()->toRfc4122();

        $response = static::createClient()->request('PATCH', '/api/tasks/' . $id, [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'status' => 'done',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'status' => 'done',
        ]);
    }

    public function testUpdateTaskWithEmptyTitleReturns422(): void
    {
        $task = new Task();
        $task->setTitle('Task to update');
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $id = $task->getId()->toRfc4122();

        $response = static::createClient()->request('PATCH', '/api/tasks/' . $id, [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'title' => '',
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    // =============================================
    // DELETE TESTS
    // =============================================

    public function testDeleteTaskReturns204(): void
    {
        $task = new Task();
        $task->setTitle('Task to delete');
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $id = $task->getId()->toRfc4122();

        $response = static::createClient()->request('DELETE', '/api/tasks/' . $id);

        $this->assertResponseStatusCodeSame(204);

        // Verify task is deleted
        $response = static::createClient()->request('GET', '/api/tasks/' . $id);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testDeleteNonExistentTaskReturns404(): void
    {
        $response = static::createClient()->request('DELETE', '/api/tasks/00000000-0000-0000-0000-000000000000');

        $this->assertResponseStatusCodeSame(404);
    }

    // =============================================
    // DUE DATE TESTS (Issue #2)
    // =============================================

    public function testCreateTaskWithFutureDueDateReturns201(): void
    {
        $futureDate = (new \DateTimeImmutable('+7 days'))->format('Y-m-d\TH:i:sP');

        $response = static::createClient()->request('POST', '/api/tasks', [
            'json' => [
                'title' => 'Task with due date',
                'dueDate' => $futureDate,
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'title' => 'Task with due date',
        ]);

        // Verify dueDate is set in response
        $data = $response->toArray();
        $this->assertArrayHasKey('dueDate', $data);
        $this->assertNotNull($data['dueDate']);
    }

    public function testCreateTaskWithTodayDueDateReturns201(): void
    {
        $today = (new \DateTimeImmutable('today'))->format('Y-m-d\TH:i:sP');

        $response = static::createClient()->request('POST', '/api/tasks', [
            'json' => [
                'title' => 'Task due today',
                'dueDate' => $today,
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testCreateTaskWithNullDueDateReturns201(): void
    {
        $response = static::createClient()->request('POST', '/api/tasks', [
            'json' => [
                'title' => 'Task without due date',
                'dueDate' => null,
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $data = $response->toArray();
        $this->assertNull($data['dueDate']);
    }

    public function testCreateTaskWithoutDueDateFieldReturns201(): void
    {
        $response = static::createClient()->request('POST', '/api/tasks', [
            'json' => [
                'title' => 'Task without dueDate field',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $data = $response->toArray();
        $this->assertNull($data['dueDate']);
    }

    public function testCreateTaskWithPastDueDateReturns422(): void
    {
        $yesterday = (new \DateTimeImmutable('yesterday'))->format('Y-m-d\TH:i:sP');

        $response = static::createClient()->request('POST', '/api/tasks', [
            'json' => [
                'title' => 'Task with past due date',
                'dueDate' => $yesterday,
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testUpdateTaskWithPastDueDateReturns422(): void
    {
        // First create a valid task
        $task = new Task();
        $task->setTitle('Task to update');
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $id = $task->getId()->toRfc4122();
        $yesterday = (new \DateTimeImmutable('yesterday'))->format('Y-m-d\TH:i:sP');

        $response = static::createClient()->request('PATCH', '/api/tasks/' . $id, [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'dueDate' => $yesterday,
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testUpdateTaskRemoveDueDateReturns200(): void
    {
        // Create task with due date
        $task = new Task();
        $task->setTitle('Task with due date');
        $task->setDueDate(new \DateTimeImmutable('+7 days'));
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $id = $task->getId()->toRfc4122();

        // Remove due date
        $response = static::createClient()->request('PATCH', '/api/tasks/' . $id, [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'dueDate' => null,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertNull($data['dueDate']);
    }

    // =============================================
    // PRIORITY TESTS (Issue #4)
    // =============================================

    public function testCreateTaskWithPriorityReturns201(): void
    {
        $response = static::createClient()->request('POST', '/api/tasks', [
            'json' => [
                'title' => 'Urgent task',
                'priority' => 'urgent',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'title' => 'Urgent task',
            'priority' => 'urgent',
        ]);
    }

    public function testCreateTaskWithoutPriorityDefaultsToMedium(): void
    {
        $response = static::createClient()->request('POST', '/api/tasks', [
            'json' => [
                'title' => 'Task without priority',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'priority' => 'medium',
        ]);
    }

    public function testFilterTasksByPriority(): void
    {
        // Create tasks with different priorities
        $lowTask = new Task();
        $lowTask->setTitle('Low priority task');
        $lowTask->setPriority(\App\Enum\TaskPriority::LOW);
        $this->entityManager->persist($lowTask);

        $highTask = new Task();
        $highTask->setTitle('High priority task');
        $highTask->setPriority(\App\Enum\TaskPriority::HIGH);
        $this->entityManager->persist($highTask);

        $this->entityManager->flush();

        // Filter by high priority
        $response = static::createClient()->request('GET', '/api/tasks?priority=high');

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertSame(1, $data['totalItems']);
        $this->assertSame('High priority task', $data['member'][0]['title']);
    }

    public function testOrderTasksByPriority(): void
    {
        // Create tasks with different priorities
        $lowTask = new Task();
        $lowTask->setTitle('Low priority task');
        $lowTask->setPriority(\App\Enum\TaskPriority::LOW);
        $this->entityManager->persist($lowTask);

        $urgentTask = new Task();
        $urgentTask->setTitle('Urgent priority task');
        $urgentTask->setPriority(\App\Enum\TaskPriority::URGENT);
        $this->entityManager->persist($urgentTask);

        $this->entityManager->flush();

        // Order by priority descending
        $response = static::createClient()->request('GET', '/api/tasks?order[priority]=desc');

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertSame(2, $data['totalItems']);
        // Urgent should come first when sorted desc (alphabetically 'urgent' > 'low')
        $this->assertSame('Urgent priority task', $data['member'][0]['title']);
    }
}
