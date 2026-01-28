<?php

namespace App;

use Pimple\Container;
use App\Storage\DataStorage;
use App\Controller\ProjectController;
use App\Controller\TaskController;

class ServiceContainer extends Container
{
    public function __construct()
    {
        parent::__construct();

        $this->registerServices();
    }

    private function registerServices()
    {
        // Database / Eloquent bootstrap
        $this['database'] = function () {
            return Database::bootFromEnv();
        };

        // Storage (depends on database being booted)
        $this['storage'] = function ($c) {
            $c['database']; // ensure Eloquent is booted
            return new DataStorage();
        };

        // Controllers
        $this[ProjectController::class] = function ($c) {
            return new ProjectController($c['storage']);
        };

        $this[TaskController::class] = function ($c) {
            return new TaskController($c['storage']);
        };
    }
}
