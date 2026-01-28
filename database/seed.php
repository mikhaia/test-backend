<?php
// database/seed.php
// Usage:
//  php database/seed.php --projects=5 --min-tasks=1 --max-tasks=10 [--truncate]
// Or: composer run db:seed

require __DIR__ . '/../vendor/autoload.php';

use App\Database;
use App\Model\Project;
use App\Model\Task;
use Illuminate\Database\Capsule\Manager as Capsule;

$options = getopt('', ['projects::', 'min-tasks::', 'max-tasks::', 'truncate', 'help']);

if (isset($options['help'])) {
    echo "Usage: php database/seed.php --projects=5 --min-tasks=1 --max-tasks=10 [--truncate]\n";
    exit(0);
}

$projects = isset($options['projects']) ? (int)$options['projects'] : 5;
$minTasks = isset($options['min-tasks']) ? (int)$options['min-tasks'] : 0;
$maxTasks = isset($options['max-tasks']) ? (int)$options['max-tasks'] : 10;
$truncate = isset($options['truncate']);

if ($minTasks > $maxTasks) {
    fwrite(STDERR, "--min-tasks must be <= --max-tasks\n");
    exit(1);
}

// bootstrap Eloquent
$capsule = Database::bootFromEnv();

$faker = Faker\Factory::create();

if ($truncate) {
    echo "Truncating tables...\n";
    $capsule->getConnection()->table('task')->truncate();
    $capsule->getConnection()->table('project')->truncate();
}

$statuses = ['todo', 'in_progress', 'done'];

$projectsInserted = 0;
$tasksInserted = 0;

for ($i = 0; $i < $projects; $i++) {
    $title = $faker->company;
    $createdAt = date('Y-m-d H:i:s', $faker->unixTime());

    try {
        $capsule->getConnection()->beginTransaction();

        $project = Project::create([
            'title' => $title,
            'created_at' => $createdAt,
        ]);
        $projectsInserted++;

        $numTasks = rand($minTasks, $maxTasks);
        for ($t = 0; $t < $numTasks; $t++) {
            $taskTitle = $faker->sentence(3);
            $status = $statuses[array_rand($statuses)];
            $taskCreated = date('Y-m-d H:i:s', $faker->unixTime());

            $project->tasks()->create([
                'title' => $taskTitle,
                'status' => $status,
                'created_at' => $taskCreated,
            ]);
            $tasksInserted++;
        }

        $capsule->getConnection()->commit();
        echo "Inserted project #{$project->id} with {$numTasks} tasks\n";
    } catch (Exception $e) {
        $capsule->getConnection()->rollBack();
        fwrite(STDERR, "Failed to insert project: " . $e->getMessage() . "\n");
    }
}

echo "Done. Projects inserted: {$projectsInserted}. Tasks inserted: {$tasksInserted}.\n";
