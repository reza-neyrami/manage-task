<?php

namespace App\Http\Controllers;

use App\Core\Interfaces\Enum\HttpCode;
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
        if (isset($_FILES['file'])) {
            $errors = array();
            $file_name = $_FILES['file']['name'];
            $file_size = $_FILES['file']['size'];
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_type = $_FILES['file']['type'];
            $file_ext = strtolower(end(explode('.', $_FILES['file']['name'])));

            $extensions = array("jpeg", "jpg", "png");

            if (in_array($file_ext, $extensions) === false) {
                $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
            }

            if ($file_size > 2097152) {
                $errors[] = 'File size must be excately 2 MB';
            }

            if (empty($errors) == true) {
                move_uploaded_file($file_tmp, "/uploads/" . $file_name);
                echo "Success";
            } else {
                print_r($errors);
            }
        }
    }

    public  function dd($data)
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
