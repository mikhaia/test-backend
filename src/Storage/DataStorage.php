<?php

namespace App\Storage;

use App\Database;
use App\Model\Project;
use App\Model\Task;
use App\Model\NotFoundException;
use Illuminate\Database\Capsule\Manager as Capsule;

class DataStorage
{
    public function __construct()
    {
        // bootstrap Eloquent
        Database::bootFromEnv();
    }

    /**
     * @param int $projectId
     * @throws NotFoundException
     */
    public function getProjectById($projectId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            throw new NotFoundException();
        }

        return $project;
    }

    /**
     * @param int $project_id
     * @param int $limit
     * @param int $offset
     */
    public function getTasksByProjectId(int $project_id, $limit, $offset)
    {
        $limit = (int) ($limit ?? 10);
        $offset = (int) ($offset ?? 0);

        $tasks = Task::where('project_id', $project_id)
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $tasks->all();
    }

    /**
     * @param array $data
     * @param int $projectId
     * @return Task
     */
    public function createTask(array $data, $projectId)
    {
        $project = $this->getProjectById($projectId);

        $payload = [
            'title' => $data['title'] ?? null,
            'status' => $data['status'] ?? 'todo',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $task = $project->tasks()->create($payload);

        return $task;
    }
}
