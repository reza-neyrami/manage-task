<?php

namespace App\Http\Controllers;

use App\Core\Repository\AuthRepository;
use App\Core\Repository\UserRepository;
use App\Core\Services\Response;



class UserController extends Controller
{
    private $userRepository;
    private $authRepositroy;

    public function __construct(UserRepository $userRepository, AuthRepository $authRepositroy)
    {
        $this->userRepository = $userRepository;
        $this->authRepositroy = $authRepositroy;
    }

    public function show($id)
    {

        $user = $this->userRepository->findById($id);

        if (!$user) {
            return Response::json(['message' => 'user not found'], 404);
        }
        return Response::json($user, 200);
    }

    public function create()
    {
        $user = $this->userRepository->create([
            'username' => 'test',
            'email' => 'neyrami.65@gmail.com',
            'password' => '1234567'
        ]);
        var_dump($user);
    }

    public function login(){
        $user = $this->authRepositroy->login([
            'email' => '<EMAIL>',
            'password' => 'password'
        ]);
        var_dump($user);
    }
}
