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

    public function showAction(Request $request)
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
}
