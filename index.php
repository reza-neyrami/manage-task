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

$router->post('/salam','HomeController@index',[JWTMiddleware::class]);
$router->get('/home/{id}','UserController@show');
$router->post('/create', 'UserController@create');


$router->post('/login','UserController@login');
// $router->get('/users', [UserController::class, 'index']);
// $router->get('/users/{id}', [UserController::class, 'show']);

$router->run();

