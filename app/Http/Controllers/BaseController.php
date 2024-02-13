<?php

namespace App\Http\Controllers;

use App\Core\Interfaces\Enum\HttpCode;
use App\Core\Services\Response;
use App\Http\Controllers\Controller;
use Exception;

class BaseController extends Controller
{

    const SUCCESS = 'Success!';
    const FAILED = 'Failed!';
    private function __construct()
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

    public function uploadFile()
    {
        if (isset($_FILES['banner'])) {
            $file = $_FILES['banner'];
            $errors = array();
            $file_name = $file['name'];
            $file_size = $file['size'];
            $file_tmp = $file['tmp_name'];
            $file_type = $file['type'];

            $file_parts = explode('.', $file['name']);
            $file_ext = strtolower(end($file_parts));

            $extensions = array("jpeg", "jpg", "png");

            if (in_array($file_ext, $extensions) === false) {
                $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
            }

            if ($file_size > 2097152) {
                $errors[] = 'File size must be exactly 2 MB';
            }

            if (empty($errors) == true) {
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/public/uploads/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $file_path = $upload_dir . $file_name;
                move_uploaded_file($file_tmp, $file_path);

                Response::json($_SERVER['HTTP_HOST'] . "/public/uploads/" . $file_name);
            } else {
                print_r($errors);
            }
        }
    }

    public function dd($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }

    public static function validateNotEmpty($value, $fieldName)
    {
        if (empty($value)) {
            throw new Exception("Field '$fieldName' cannot be empty.");
        }
    }
}
