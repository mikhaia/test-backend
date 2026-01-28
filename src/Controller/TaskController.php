<?php

namespace App\Controller;

use App\Exception\ValidationException;
use App\Storage\DataStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Validation;

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
        $limit = (int) $request->attributes->get('limit', 10);
        $offset = (int) $request->attributes->get('offset', 0);

        // Validate limit and offset
        $validator = Validation::createValidator();
        $limitConstraint = new Range(['min' => 1, 'max' => 100]);
        $offsetConstraint = new Range(['min' => 0]);

        $limitErrors = $validator->validate($limit, $limitConstraint);
        $offsetErrors = $validator->validate($offset, $offsetConstraint);

        if (count($limitErrors) > 0 || count($offsetErrors) > 0) {
            $errors = [];
            foreach ($limitErrors as $error) {
                $errors[] = 'limit: ' . $error->getMessage();
            }
            foreach ($offsetErrors as $error) {
                $errors[] = 'offset: ' . $error->getMessage();
            }
            throw new ValidationException($errors);
        }

        $tasks = $this->storage->getTasksByProjectId($projectId, $limit, $offset);
        return new JsonResponse($tasks, 200);
    }

    public function createAction(Request $request)
    {
        $projectId = $request->attributes->get('id');

        $project = $this->storage->getProjectById($projectId);

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            throw new ValidationException(['Invalid JSON']);
        }

        // Validate title
        $validator = Validation::createValidator();
        $constraints = new Collection([
            'title' => [
                new NotBlank(['message' => 'Title is required']),
                new Length(['max' => 255, 'maxMessage' => 'Title must be at most {{ limit }} characters']),
            ],
        ]);

        $violations = $validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            throw new ValidationException($errors);
        }

        $task = $this->storage->createTask($data, $project->getId());
        $response = new JsonResponse($task->toArray(), 201);
        $response->headers->set('Location', sprintf('/project/%d/tasks/%d', $project->getId(), $task->getId()));
        return $response;
    }
}
