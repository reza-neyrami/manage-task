<?php

namespace App\Http\Controllers;

use App\Core\Repository\AuthRepository;
use App\Core\Repository\UserRepository;
use App\Core\Services\Auth;
use App\Core\Services\Request;
use App\Core\Services\Response;

class UserController extends BaseController
{
    private $userRepository;
    private $authRepository;
    protected $request;

    public function __construct(UserRepository $userRepository, AuthRepository $authRepository, Request $request)
    {
        $this->userRepository = $userRepository;
        $this->authRepository = $authRepository;
        $this->request = $request;
    }

    // دریافت اطلاعات یک کاربر
    public function getUser(int $id)
    {
        if (isset($id)) {

            return $this->userRepository->findById($id);
        }

    }
    // دریافت اطالاع  کاربر بر اساس فیلد و مقدار
    public function getUsersBySomeField(string $field, string $value)
    {
        try {
            $users = $this->userRepository->findBy($field, $value);
            return Response::json($users, 200);
        } catch (\Exception $e) {
            return Response::json(['message' => 'There was an error getting the users. ,' . $e->getMessage()], 500);
        }
    }

    // آپدیت و ذخیره کاربران
    public function upOrCreateUser()
    {
        try {
            $userData = $this->getUserData();
            $this->validateUserData($userData);

            if (Auth::user()->role != 'admin') {
                throw new \Exception('لطفا لاگین کنید ', 400);
            }

            if ($this->request->id && $this->request->email) {
                // اگر شناسه کاربر وجود دارد، کاربر را به‌روزرسانی می‌کنیم
                $user = $this->userRepository->findById($this->request->id);
                if (!$user) {
                    throw new \Exception('Not Found Users', 400);
                }
                $this->userRepository->update($this->request->id, $userData);
                return Response::json(['message' => 'User updated successfully.'], 200);

            }

            // dd(error_get_last());
            // در غیر این صورت، یک کاربر جدید ایجاد می‌کنیم
            $user = $this->userRepository->create($userData);
            return json_encode($user);
        } catch (\Exception $e) {
            return Response::json(['message' => 'There was an error. ,' . $e->getMessage()], 500);
        }
    }

    // ایجاد کاربر
    public function createUser()
    {
        try {
            $userData = $this->getUserData();
            $this->validateUserData($userData);
            if (Auth::user()->role != 'admin') {
                throw new \Exception('لطفا لاگین کنید ', 400);
            }

            $user = $this->userRepository->create($userData);
            return json_encode($user);
        } catch (\Exception $e) {
            return Response::json(['message' => 'There was an error creating the user. ,' . $e->getMessage()], 500);
        }
    }

    // دریافت فیلد های مربوط به اطلاعات کل کاربران
    private function getUserData()
    {
        return [
            'username' => $this->request->username,
            'email' => $this->request->email,
            'password' => password_hash($this->request->password, PASSWORD_DEFAULT),
            'role' => $this->request->role,
        ];
    }

    // در صورت خالی بودن validation execute
    private function validateUserData($data)
    {
        $requiredFields = ['username', 'password', 'role', 'email'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new \Exception("The field '{$field}' is required.");
            }
        }
    }

    // ویرایش کاربران
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

    // دریافت اطلاعات کاربران بر اساس نام کاربری
    public function getTaskByUserId()
    {
        try {
            $user = Auth::user();
            $tasks = $this->userRepository->getTaskByUserId($user->id);
            return $tasks;
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

    // دریافت اطلاعات کل کاربران
    public function getAllUsers()
    {
        try {
            $users = $this->userRepository->paginate($this->request->page ?? 1, $this->request->perPage ?? 10);
            return Response::json($users, 200);
        } catch (\Exception $e) {
            return Response::json(['message' => 'There was an error getting the users. ,' . $e->getMessage()], 500);
        }
    }

    public function getPaginatedUsers(int $page = 15, int $per_page = 1)
    {
        try {
            $users = $this->userRepository->paginate($page, $per_page);
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
