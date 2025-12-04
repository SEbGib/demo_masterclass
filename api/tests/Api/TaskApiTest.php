<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Enum\TaskStatus;

class TaskApiTest extends ApiTestCase
{
    public function testCreateTask(): void
    {
        static::createClient()->request('POST', '/api/tasks', [
            'json' => [
                'title' => 'My first task',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'title' => 'My first task',
            'status' => TaskStatus::PENDING->value,
        ]);
    }

    public function testListTasks(): void
    {
        static::createClient()->request('GET', '/api/tasks');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testUpdateTaskStatus(): void
    {
        $client = static::createClient();

        // create
        $response = $client->request('POST', '/api/tasks', [
            'json' => ['title' => 'Update status test'],
        ]);

        $data = $response->toArray();
        $iri = $data['@id'];

        // patch
        $client->request('PATCH', $iri, [
            'json' => ['status' => TaskStatus::DONE->value],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'status' => TaskStatus::DONE->value,
        ]);
    }

    public function testDeleteTask(): void
    {
        $client = static::createClient();

        $response = $client->request('POST', '/api/tasks', [
            'json' => ['title' => 'To be deleted'],
        ]);

        $iri = $response->toArray()['@id'];

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);
    }
}
