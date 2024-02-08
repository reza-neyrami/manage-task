<?php

namespace App\Core\Interfaces\Auth;


interface AuthRepositoryInterface
{
    public function login(array $data): array;
    public function register(array $data): array;

}
