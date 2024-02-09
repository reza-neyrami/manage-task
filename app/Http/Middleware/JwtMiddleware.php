<?php

namespace App\Http\Middleware;

use App\Core\Services\JWTApi;
use App\Core\Services\Middleware;

class JWTMiddleware extends Middleware
{
  

    public  function handle($request, $next)
    {
        // $headers = apache_request_headers();
    
       
        $jwt_token = header('Authorization');
        var_dump($jwt_token);
        if (!JWTApi::validate_jwt_token($jwt_token, $_ENV['JWT_SECRET'])) {
            header('HTTP/1.0 401 Unauthorized');
            echo "توکن شما نامعتبر است. لطفا دوباره وارد شوید.";
            exit();
        }

        return $next($request);
    }
}
