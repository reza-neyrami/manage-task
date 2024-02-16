<?php

namespace App\Http\Controllers;

use App\Core\Repository\AuthRepository;
use App\Core\Services\JWTApi;
use App\Core\Services\Request;
use App\Core\Services\Response;
use App\Http\Controllers\BaseController;

class AuthController extends BaseController
{
    private $authRepositroy;
    public $request;

    public function __construct(AuthRepository $authRepositroy, Request $request)
    {
        $this->authRepositroy = $authRepositroy;
        $this->request = $request;
    }

    public function login()
    {

        $email = $this->request->email;
        $password = $this->request->password;

        if (!$email && !$password) {
            return Response::json(['status' => 'error', 'message' => 'لطفا نام کاربری و ایمیل خود را وارد نمایید'], 400);
        }
        $login = $this->authRepositroy->login([
            'email' => $email,
            'password' => $password,
        ]);
        if ($login['status'] == false) {
            return Response::json($login, 401);
        }

        $jwt_token = JWTApi::generate_jwt_token($login['user_id']);
        return Response::json([
            'access_token' => $jwt_token,
            "message" => $login,
        ], 200);
    }

    public function logout()
    {

        $jwt_token = $this->request->header('Authorization');
        if (!isset($jwt_token)) {
            return Response::json([
                "message" => "token not found",
            ], 404);
        }
        $logout = $this->authRepositroy->logout($jwt_token);
        if ($logout['status'] == false) {
            return Response::json($logout, 401);
        }
        return Response::json([
            "message" => $logout,
        ], 200);
    }

    public function register()
    {
        $register = $this->authRepositroy->register([
            'username' => $this->request->username,
            'email' => $this->request->email,
            'password' => $this->request->password,
            'role' => $this->request->role ?? 'programmer',
        ]);
        if ($register['status'] == false) {
            return Response::json($register, 401);
        }
        $jwt_token = JWTApi::generate_jwt_token($register['user_id']);
        return Response::json([
            "message" => $register,
            "access_token" => $jwt_token,
        ], 200);
    }
}
