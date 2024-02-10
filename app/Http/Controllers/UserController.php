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
    protected $request;

    public function __construct(UserRepository $userRepository, Request $request)
    {
        $this->userRepository = $userRepository;
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

    public function all(){
        $user = $this->userRepository->all();
        return Response::json($user, 201);
    }

   
}
