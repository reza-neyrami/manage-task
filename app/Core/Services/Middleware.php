<?php

namespace App\Core\Services;


abstract class Middleware
{
    abstract public  function handle($request, $next);
}
