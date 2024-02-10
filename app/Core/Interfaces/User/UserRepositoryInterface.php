<?php

namespace App\Core\Interfaces\User;

use App\Model\User;

interface UserRepositoryInterface
{
    public function model();
    public function findById(int $id): User;
    public function findByEmail(string $email): ?User;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
    public function all():array;
    public function paginate(int $limit = 15, int $page = 1): array;
    public function findBy(string $field, string $value): ?User;
}
