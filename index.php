<?php


require __DIR__ . "/vendor/autoload.php";




$router = new \App\Core\Router();


$router->get('/home/{id}','UserController@show');
// $router->get('/users', [UserController::class, 'index']);
// $router->get('/users/{id}', [UserController::class, 'show']);

$router->run();

