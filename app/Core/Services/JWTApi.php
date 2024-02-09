<?php

namespace App\Core\Services;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTApi
{

    private static $secret;

    public function __construct($secret)
    {
        self::$secret = $secret;
    }

    public static function decode_jwt_token($jwt_token)
    {
        $decoded_token = JWT::decode($jwt_token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        return $decoded_token;
    }

    public static function  generate_jwt_token($user_id)
    {
        $issued_at = time();
        $expiration_time = $issued_at + (60 * 60 * 10); // valid for 1 hour

        $payload = array(
            'iat' => $issued_at,
            'exp' => $expiration_time,
            'sub' => $user_id
        );

        return JWT::encode($payload, $_ENV['JWT_SECRET'], "HS256");
    }

    public static function validate_jwt_token($jwt_token)
    {
        try {
            self::decode_jwt_token($jwt_token, $_ENV['JWT_SECRET']);
            return true;
        } catch (ExpiredException $e) {
            // توکن منقضی شده است
            header('HTTP/1.0 401 Unauthorized');
            return Response::json(['message' => '   انقضای احراز هویت لطفا لاگین کنید'], 401);
            exit();
        } catch (Exception $e) {
            // توکن نامعتبر است
            header('HTTP/1.0 401 Unauthorized');
            return Response::json(['message' => '  احراز هویت نامعتبر .لطفا لاگین کنید'], 401);
            exit();
        }
    }
}


