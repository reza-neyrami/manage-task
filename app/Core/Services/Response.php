<?php

namespace App\Core\Services;

class Response
{
    public static function json(mixed $data, $statusCode = 200)
    {
        if (is_array($data)) {
            header('Content-Type: application/json');
            http_response_code($statusCode);
            echo json_encode($data);
            exit;
        }elseif (is_string($data)) {
            header('Content-Type: application/json');
            http_response_code($statusCode);
            echo $data;
            exit;
        }
        header('Content-Type: application/json');
        return $data;
    }
}
