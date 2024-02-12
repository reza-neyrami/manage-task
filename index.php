<?php

use App\Core\Router;
use App\Core\Services\Container;
use App\Core\Services\Request;
use App\Http\Middleware\ApiMiddleware;
use App\Http\Middleware\JWTMiddleware;

use Dotenv\Dotenv;

require __DIR__ . "/vendor/autoload.php";

$env = Dotenv::createImmutable(__DIR__);
$env->load();

// class Database {
//     use DatabaseConnectionTrait;
// }

// $database = new Database();
// $database->runSql();


$container = new Container;
$request = new Request;
$router = new Router($container, $request);


$router->group('/tasks', [], [], function ($tasks) {

    $tasks->get('/', 'TaskController@getAllTasks');
    $tasks->get('/{id}', 'TaskController@getTask');
    $tasks->post('/create', 'TaskController@createTask');
    $tasks->get('/user', 'TaskController@taskByAuthId');
    $tasks->put('/{id}', 'TaskController@updateTask');
    $tasks->delete('/{id}', 'TaskController@deleteTask');
    $tasks->get('/user/{id}', 'TaskController@getTasksByUserId');
    $tasks->get('/users', 'TaskController@getUsers');
    $tasks->post('/assignuser/{taskId}', 'TaskController@assignTask', [JWTMiddleware::class]);
});

$router->group('/users', [], [], function ($user) {
    $user->get('/', 'UserController@getAllUsers');
    $user->get('/{id}', 'UserController@getUser');
    $user->post('/', 'UserController@createUser');
    $user->put('/{id}', 'UserController@updateUser');
    $user->delete('/{id}', 'UserController@deleteUser');
});

$router->group('/files', [JWTMiddleware::class], [], function ($files) {
    $files->post('/upload/{taskId}', 'FileController@uploadFile');
    $files->get('/{taskId}', 'FileController@getFilesByTaskId');
});

$router->group('/auth', [ApiMiddleware::class], [], function ($auth) {
    $auth->post('/login', 'AuthController@login');
    $auth->post('/register', 'AuthController@register');
});

$router->run();
// $api = new API($router);
// $api->Task();
