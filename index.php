<?php

use App\Core\Router;
use App\Core\Services\Container;

require __DIR__ . "/vendor/autoload.php";




$container = new Container;
// Bind your controllers to the container here
$router = new Router($container);



$router->get('/home/{id}','UserController@show');
$router->post('/create','UserController@create');
// $router->get('/users', [UserController::class, 'index']);
// $router->get('/users/{id}', [UserController::class, 'show']);

$router->run();

