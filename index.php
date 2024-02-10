<?php

use App\Core\Router;
use App\Core\Services\Container;
use App\Core\Services\Request;
use App\Http\Middleware\JWTMiddleware;
use Dotenv\Dotenv;

require __DIR__ . "/vendor/autoload.php";

$env = Dotenv::createImmutable(__DIR__);
$env->load();
// class Database {
//     use DatabaseConnectionTrait;
// }


// $database = new Database();


$container = new Container;
// Bind your controllers to the container here
$router = new Router($container);

$router->post('/auth', 'HomeController@index');
$router->get('/home/{id}', 'UserController@show');
$router->post('/create', 'UserController@create');
$router->get('/users', 'UserController@all');


$router->post('/login', 'AuthController@login');


$router->group('/tasks', [], [], function ($router) {
        $router->get('/', 'TaskController@getAllTasks');
        $router->get('/{id}', 'TaskController@getTask');
        $router->post('/', 'TaskController@createTask');
        $router->put('/{id}', 'TaskController@updateTask');
        $router->delete('/{id}', 'TaskController@deleteTask');
        $router->get('/user/{userId}', 'TaskController@getTasksByUserId');
});




$router->run();
