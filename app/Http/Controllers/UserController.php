<?php

namespace App\Http\Controllers;

use App\Core\Repository\UserRepository;
use App\Core\Services\Response;



class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function show($id)
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return Response::json(['message' => 'user not found'], 404);
        }
        return Response::json($user, 200);;
    }

    
}
