<?php

namespace App\Core\Services;

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
}

