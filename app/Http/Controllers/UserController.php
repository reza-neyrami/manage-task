<?php

namespace App\Http\Controllers;

use App\Core\Repository\UserRepository;
use App\Core\Services\Auth;
use App\Core\Services\Request;
use App\Core\Services\Response;

class UserController extends BaseController
{
    private $userRepository;
    protected $request;

    public function __construct(UserRepository $userRepository, Request $request)
    {
        $this->userRepository = $userRepository;
        $this->request = $request;
    }

    public function getUser(int $id)
    {
        if (isset($id)) {

            return $this->userRepository->findById($id);
        }

    }

    public function getUsersBySomeField(string $field, string $value)
    {
        try {
            $users = $this->userRepository->findBy($field, $value);
            return Response::json($users, 200);
        } catch (\Exception $e) {
            return Response::json(['message' => 'There was an error getting the users. ,' . $e->getMessage()], 500);
        }
    }

    public function createUser()
    {
        try {
            $user = $this->userRepository->create($this->getUserData());
            return Response::json($user, 201);
        } catch (\Exception $e) {
            return Response::json(['message' => 'There was an error creating the user. ,' . $e->getMessage()], 500);
        }
    }

    private function getUserData()
    {
        return $this->request->all();
    }

    public function gettaskByUser()
    {
        $user = Auth::user();
        $tasks = $user->task();
        return Response::json($tasks, 200);
    }

    public function getUserSkile(string $skile)
    {
        $user = Auth::user();
        if ($user->role != 'admin') {
            return Response::json(['message' => 'شما به این بخش دسترسی ندارید'], 400);
        } 
        
        $skilesUsers = $this->userRepository->getBy('role', $skile);


        return Response::json($skilesUsers, 200);
    }

    public function updateUser(int $id)
    {
        try {
            $data = array_merge($this->getUserData(), ['status' => 'active']);
            $this->userRepository->update($id, $data);
            return Response::json(['message' => 'User updated successfully.'], 200);
        } catch (\Exception $e) {
            return Response::json(['message' => 'There was an error updating the user. ,' . $e->getMessage()], 500);
        }
    }

    public function deleteUser(int $id)
    {
        try {
            $this->userRepository->delete($id);
            return Response::json(['message' => 'User deleted successfully.'], 200);
        } catch (\Exception $e) {
            return Response::json(['message' => 'There was an error deleting the user. ,' . $e->getMessage()], 500);
        }
    }

    public function getAllUsers()
    {
        try {
            $users = $this->userRepository->all();
            return Response::json($users, 200);
        } catch (\Exception $e) {
            return Response::json(['message' => 'There was an error getting the users. ,' . $e->getMessage()], 500);
        }
    }

    public function getPaginatedUsers(int $limit = 15, int $page = 1)
    {
        try {
            $users = $this->userRepository->paginate($limit, $page);
            return Response::json($users, 200);
        } catch (\Exception $e) {
            return Response::json(['message' => 'There was an error getting the users. ,' . $e->getMessage()], 500);
        }
    }

    public function getUserBy(string $field, string $value)
    {
        try {
            $user = $this->userRepository->findBy($field, $value);
            return Response::json($user, 200);
        } catch (\Exception $e) {
            return Response::json(['message' => 'There was an error getting the user. ,' . $e->getMessage()], 500);
        }
    }
}
