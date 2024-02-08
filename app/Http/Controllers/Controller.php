<?php

namespace App\Http\Controllers;

use App\Core\Interfaces\Enum\HttpCode;
class Controller
{
    const SUCCESS = 'Success!';
    const FAILED = 'Failed!';
    public function __construct()
    {
        
    }

    protected function apiResponse($data = [], $status = self::SUCCESS, $code = HttpCode::SUCCESS, $message = '')
    {
        return json_encode([
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
