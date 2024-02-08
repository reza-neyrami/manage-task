<?php

use App\Core\Router;
use App\Core\Services\Container;

require __DIR__ . "/vendor/autoload.php";


// class Database {
//     use DatabaseConnectionTrait;
// }


// $database = new Database();


$container = new Container;
// Bind your controllers to the container here
$router = new Router($container);

$router->post('/salam','HomeController@index');
$router->get('/home/{id}','UserController@show');
$router->post('/create','UserController@create');
$router->post('/login','UserController@login');
// $router->get('/users', [UserController::class, 'index']);
// $router->get('/users/{id}', [UserController::class, 'show']);

$router->run();

