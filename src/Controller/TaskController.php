<?php

namespace App\Controller;

use App\Model\NotFoundException;
use App\Storage\DataStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TaskController
{
    /** @var DataStorage */
    private $storage;

    public function __construct(DataStorage $storage)
    {
        $this->storage = $storage;
    }

    public function listAction(Request $request)
    {
        $projectId = $request->attributes->get('id');
        $limit = $request->attributes->get('limit', 10);
        $offset = $request->attributes->get('offset', 0);

        $tasks = $this->storage->getTasksByProjectId($projectId, $limit, $offset);
        return new JsonResponse($tasks, 200);
    }

    public function createAction(Request $request)
    {
        $projectId = $request->attributes->get('id');

        try {
            $project = $this->storage->getProjectById($projectId);
        } catch (NotFoundException $e) {
            return new JsonResponse(['error' => 'Project not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data) || empty($data['title'])) {
            return new JsonResponse(['error' => 'Validation error', 'details' => ['title is required']], 400);
        }

        $task = $this->storage->createTask($data, $project->getId());
        $response = new JsonResponse($task->toArray(), 201);
        $response->headers->set('Location', sprintf('/project/%d/tasks/%d', $project->getId(), $task->getId()));
        return $response;
    }
}
