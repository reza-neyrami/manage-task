<?php

namespace App\Core\Repository;

use App\Core\Interfaces\Auth\AuthRepositoryInterface;
use App\Core\Services\JWTApi;
use App\Model\User;

class AuthRepository implements AuthRepositoryInterface
{
    private $model;

    public function __construct(User $user)
    {

        $this->model = $user;
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
       return ['message' => 'user logged in successfully', 'user_id'=>$user->id,'status' => true];
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

    public function logout(string $token): array
    {

        $decoded = JWTApi::decode_jwt_token($token);

        if ($decoded['status'] == false) {
            return ['message' => 'invalid token'];
        }
        //todo: delete token from database ...of course you can add token to model user table and delete it from there
        $user = $this->model->find($decoded['data']['user_id']);
        if (!$user) {
            return ['message' => 'user not found'];
        }
        $user->token = null;
        $user->save();
        return ['message' => 'user logged out successfully'];
    }
}
