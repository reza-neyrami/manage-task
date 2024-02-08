<?php

namespace App\Core\Services;
class Response
{
    public static function json(array $data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode( $data);
        exit;
    }
}



