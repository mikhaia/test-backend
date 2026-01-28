<?php

namespace App;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class Database
{
    public static function bootFromEnv(array $options = [])
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db   = getenv('DB_NAME') ?: 'task_tracker';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASSWORD') ?: '';
        $port = getenv('DB_PORT') ?: null;

        $capsule = new Capsule();

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $host,
            'database'  => $db,
            'username'  => $user,
            'password'  => $pass,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'port'      => $port ?: 3306,
        ]);

        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule;
    }
}
