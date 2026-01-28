<?php

namespace App;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class Routes
{
    public static function getRoutes(): RouteCollection
    {
        $routes = new RouteCollection();

        // GET /project/{id}
        $routes->add('project_show', new Route('/project/{id}', [
            '_controller' => 'App\Controller\ProjectController',
            '_action' => 'showAction',
        ], ['id' => '\d+'], [], '', [], ['GET']));

        // GET /project/{id}/tasks
        $routes->add('project_tasks', new Route('/project/{id}/tasks/{limit}/{offset}', [
            '_controller' => 'App\Controller\TaskController',
            '_action' => 'listAction',
        ], [
            'id' => '\d+',
            'limit' => '\d+',
            'offset' => '\d+'
        ], [], '', [], ['GET']));

        // POST /project/{id}/tasks
        $routes->add('project_create_task', new Route('/project/{id}/tasks', [
            '_controller' => 'App\Controller\TaskController',
            '_action' => 'createAction',
        ], ['id' => '\d+'], [], '', [], ['POST']));

        return $routes;
    }
}
