<?php

namespace App\Core\Repository;

use App\Core\Interfaces\Auth\AuthRepositoryInterface;


class TaskRepository implements AuthRepositoryInterface
{
    private $model;

    public function __construct(Task $task)
    {

        $this->model = $task;
    }

    public function login(array $data): array
    {
        $user = $this->model->where('email', $data['email'])->first();
        if (!$user) {
            return ['message' => 'user not found'];
        }
        if (!password_verify($data['password'], $user->password)) {
            return ['message' => 'invalid password'];
        }
        return ['message' => 'logged in successfully', 'status' => true];
    }

    public function register(array $data): array
    {
        $user = $this->model->where('email', $data['email'])->first();
        if ($user) {
            return ['message' => 'user already exists'];
        }
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->model->create($data);
        return ['message' => 'user created successfully', 'status' => true];
    }
}
