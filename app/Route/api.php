<?php

namespace App\Route;

use App\Http\Middleware\JWTMiddleware;

class API
{
    protected $router;
    public  function __construct($router)
    {
        $this->router = $router;
    }


    public static function User($router)
    {
       
    }
}
