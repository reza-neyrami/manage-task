<?php

namespace App\Core\Services;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTApi
{

    private $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    public static function decode_jwt_token($jwt_token, $secret_key)
    {
        $decoded_token = JWT::decode($jwt_token,  new Key($secret_key, 'HS256'));
        return $decoded_token;
    }

    public static function  generate_jwt_token($user_id, $secret_key)
    {
        $issued_at = time();
        $expiration_time = $issued_at + (60 * 60); // valid for 1 hour

        $payload = array(
            'iat' => $issued_at,
            'exp' => $expiration_time,
            'sub' => $user_id
        );

        return JWT::encode($payload, $secret_key, "HS256");
    }

    public static function validate_jwt_token($jwt_token, $secret_key)
    {
        try {
            $decoded_token = self::decode_jwt_token($jwt_token, $secret_key);
            return true;
        } catch (ExpiredException $e) {
            // توکن منقضی شده است
            header('HTTP/1.0 401 Unauthorized');
            echo "توکن شما منقضی شده است. لطفا دوباره وارد شوید.";
            exit();
        } catch (Exception $e) {
            // توکن نامعتبر است
            header('HTTP/1.0 401 Unauthorized');
            echo "توکن شما نامعتبر است. لطفا دوباره وارد شوید.";
            exit();
        }
    }
}
