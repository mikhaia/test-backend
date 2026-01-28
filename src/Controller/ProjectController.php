<?php

namespace App\Controller;

use App\Model\NotFoundException;
use App\Storage\DataStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProjectController
{
    /** @var DataStorage */
    private $storage;

    public function __construct(DataStorage $storage)
    {
        $this->storage = $storage;
    }

    public function projectAction(Request $request)
    {
        $id = $request->attributes->get('id');

        try {
            $project = $this->storage->getProjectById($id);
            return new JsonResponse($project->toArray(), 200);
        } catch (NotFoundException $e) {
            return new JsonResponse(['error' => 'Project not found'], 404);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Something went wrong'], 500);
        }
    }

    public function projectTaskPagerAction(Request $request)
    {
        $id = $request->attributes->get('id');
        $limit = $request->query->get('limit', 10);
        $offset = $request->query->get('offset', 0);

        $tasks = $this->storage->getTasksByProjectId($id, $limit, $offset);
        return new JsonResponse($tasks, 200);
    }

    public function projectCreateTaskAction(Request $request)
    {
        $id = $request->attributes->get('id');

        try {
            $project = $this->storage->getProjectById($id);
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
