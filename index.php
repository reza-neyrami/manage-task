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


$router->group('/auth', [ApiMiddleware::class], [], function ($auth) {
    $auth->post('/login', 'AuthController@login');
    $auth->post('/register', 'AuthController@register');
});

$router->group('/files', [JWTMiddleware::class], [], function ($files) {
    $files->post('/upload', 'ReportController@uploadFile');
    $files->get('/{taskId:int}', 'ReportController@getFilesByTaskId');
    $files->post('/create', 'ReportController@createReport');
});

$router->group('/users', [], [], function ($user) {
    $user->get('/', 'UserController@getAllUsers');
    $user->get('/{id:int}', 'UserController@getUser');
    $user->get('/skile/{skile:string}', 'UserController@getUserSkile');
    $user->post('/', 'UserController@createUser');
    $user->put('/{id:int}', 'UserController@updateUser');
    $user->delete('/{id:int}', 'UserController@deleteUser');
    $user->get('/getTaskUser', 'UserController@getTaskByUserId');
});

$router->group('/tasks', [], [], function ($tasks) {
    $tasks->get('/', 'TaskController@getAllTasks');
    $tasks->get('/{id:int}', 'TaskController@getTask');
    $tasks->post('/create', 'TaskController@createTask');
    $tasks->get('/user', 'TaskController@taskByAuthId');
    $tasks->put('/{id:int}', 'TaskController@updateTask');
    $tasks->delete('/{id:int}', 'TaskController@deleteTask');
    $tasks->get('/user/{id:int}', 'TaskController@getTasksByUserId');
    $tasks->get('/users', 'TaskController@getUsers');
    $tasks->post('/assignuser/{taskId:int}', 'TaskController@assignTask', [JWTMiddleware::class]);
    
});


$router->group('/usertasks', [], [], function ($usertask) {
    $usertask->post('/status/{taskId:int}', 'UserTaskController@userStatusUpdate', [JWTMiddleware::class]);
});

$router->run();
// $api = new API($router);
// $api->Task();
