<?php

namespace App\Http\Controllers;

use App\Core\Repository\AuthRepository;
use App\Core\Repository\UserRepository;
use App\Core\Services\JWTApi;
use App\Core\Services\Request;
use App\Core\Services\Response;



class UserController extends Controller
{
    private $userRepository;
    private $authRepositroy;
    protected $request;

    public function __construct(UserRepository $userRepository, AuthRepository $authRepositroy, Request $request)
    {
        $this->userRepository = $userRepository;
        $this->authRepositroy = $authRepositroy;
        $this->request = $request;
    }

    public function show($id)
    {

        $user = $this->userRepository->findById($id);

        if (!$user) {
            return Response::json(['message' => 'user not found'], 404);
        }
        return $user;
    }

    public function create()
    {
        $user = $this->userRepository->create([
            'username' => $this->request->get('username'),
            'email' => $this->request->get('email'),
            'password' =>  password_hash($this->request->get('password'), PASSWORD_DEFAULT),
            'role' => $this->request->get('role') ?? 'programmer'
        ]);

        return Response::json($user, 201);
    }

    public function login()
    {
        $login =  $this->authRepositroy->login([
            'email' => $this->request->get('email'),
            'password' => $this->request->get('password')
        ]);
        if ($login['status'] == false) {
            return Response::json($login, 401);
        }
 
        $jwt_token = JWTApi::generate_jwt_token($login['user_id'], $_ENV['JWT_SECRET']);
        return Response::json([
            'access_token' => $jwt_token,
            "message" => $login,
        ], 200);
        // var_dump($this->request->get('email'));
    }
}
