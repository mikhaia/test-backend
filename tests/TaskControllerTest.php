<?php

namespace Tests;

use App\Exception\ValidationException;
use App\Controller\TaskController;
use App\Storage\DataStorage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskControllerTest extends TestCase
{
    private $controller;
    private $storage;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the storage
        $this->storage = $this->createMock(DataStorage::class);

        // Create controller
        $this->controller = new TaskController($this->storage);
    }

    public function testCreateActionValidatesRequiredTitle()
    {
        // Create request with empty title
        $request = Request::create('/project/1/tasks', 'POST', [], [], [], [], '{"title": ""}');
        $request->attributes->set('id', 1);

        // Mock storage methods - getProjectById will be called before validation
        $project = $this->createMock(\App\Model\Project::class);
        $this->storage->expects($this->once())->method('getProjectById')->willReturn($project);
        $this->storage->expects($this->never())->method('createTask');

        // Expect ValidationException
        $this->expectException(ValidationException::class);

        $this->controller->createAction($request);
    }

    public function testCreateActionValidatesTitleLength()
    {
        // Create request with title that's too long
        $longTitle = str_repeat('a', 256);
        $request = Request::create('/project/1/tasks', 'POST', [], [], [], [], '{"title": "' . $longTitle . '"}');
        $request->attributes->set('id', 1);

        // Mock storage methods - getProjectById will be called before validation
        $project = $this->createMock(\App\Model\Project::class);
        $this->storage->expects($this->once())->method('getProjectById')->willReturn($project);
        $this->storage->expects($this->never())->method('createTask');

        // Expect ValidationException
        $this->expectException(ValidationException::class);

        $this->controller->createAction($request);
    }

    public function testListActionValidatesLimitRange()
    {
        // Create request with invalid limit
        $request = Request::create('/project/1/tasks/0/200', 'GET');
        $request->attributes->set('id', 1);
        $request->attributes->set('limit', 200); // Above max 100
        $request->attributes->set('offset', 0);

        // Mock storage methods
        $this->storage->expects($this->never())->method('getTasksByProjectId');

        // Expect ValidationException
        $this->expectException(ValidationException::class);

        $this->controller->listAction($request);
    }

    public function testListActionValidatesOffsetRange()
    {
        // Create request with invalid offset
        $request = Request::create('/project/1/tasks/0/-1', 'GET');
        $request->attributes->set('id', 1);
        $request->attributes->set('limit', 10);
        $request->attributes->set('offset', -1); // Below min 0

        // Mock storage methods
        $this->storage->expects($this->never())->method('getTasksByProjectId');

        // Expect ValidationException
        $this->expectException(ValidationException::class);

        $this->controller->listAction($request);
    }
}
