<?php

namespace App\Core\Services;

use App\Model\User;
use Exception;

class Auth
{

   private function __construct(){
    // private constructor to prevent creating a new instance of the
     // *Singleton* via the `new` operator from outside of this class.
   }

    public static function __callStatic($method, $args)
    {
        $headers = apache_request_headers();
        $jwt_token = $headers['Authorization'] ?? NULL;
        if (!isset($jwt_token)) {
            throw new Exception('Unauthorized FOR  Headers');
            // return Response::json(['message' => 'token not found'], 404);
            
        }elseif (!JWTApi::validate_jwt_token($jwt_token)) {
            throw new Exception('Unauthorized for token');
        }

        return call_user_func_array([self::class, $method], $args);
    }

    private static function user()
    {
        $headers = apache_request_headers();
        $jwt_token = $headers['Authorization'];
        return User::find(JWTApi::decode_jwt_token($jwt_token)->sub);
    }
}
