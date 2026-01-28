<?php

namespace App\Controller;

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

        $project = $this->storage->getProjectById($id);
        return new JsonResponse($project->toArray(), 200);
    }
}
