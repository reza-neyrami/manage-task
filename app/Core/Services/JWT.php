<?php

namespace App\Core\Services;

class JWT
{

    private $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    public function encode($data)
    {
        $header = array(
            "typ" => "JWT",
            "alg" => "HS256"
        );

        $payload = array(
            "data" => $data,
            "iat" => time()
        );

        $header = json_encode($header);
        $payload = json_encode($payload);

        $base64UrlHeader = base64_encode($header);
        $base64UrlPayload = base64_encode($payload);

        $signature = hash_hmac("sha256", $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = base64_encode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function decode($token)
    {
        $parts = explode(".", $token);

        if (count($parts) !== 3) {
            return false;
        }

        $header = json_decode(base64_decode($parts[0]));
        $payload = json_decode(base64_decode($parts[1]));

        if ($header->alg !== "HS256") {
            return false;
        }

        $signature = hash_hmac("sha256", $parts[0] . "." . $parts[1], $this->secret, true);
        $base64UrlSignature = base64_encode($signature);

        if ($base64UrlSignature !== $parts[2]) {
            return false;
        }

        return $payload->data;
    }

    public function verify($token)
    {
        $decoded = $this->decode($token);

        if ($decoded === false) {
            return false;
        }

        if (isset($decoded->exp) && time() > $decoded->exp) {
            return false;
        }

        return true;
    }
}
