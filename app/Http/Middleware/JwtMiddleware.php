<?php

namespace App\Http\Middleware;

use App\Core\Services\JWTApi;
use App\Core\Services\Middleware;
use App\Core\Services\Response;

class JWTMiddleware extends Middleware
{


    public  function handle($request, $next)
    {
        $headers = apache_request_headers();
        $jwt_token = $headers['Authorization'] ?? null;
        
        if (!isset($jwt_token)) {
            return Response::json(['message' => 'token not found'], 404);
        }elseif(!JWTApi::validate_jwt_token($jwt_token)) {
            header('HTTP/1.0 401 Unauthorized');
            exit();
        }

        return $next($request);
    }
}
