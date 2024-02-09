<?php

namespace App\Core\Interfaces\Task;

use App\Model\Task;

interface TaskRepositoryInterface
{
    public function findById(int $id): ?Task;
    // public function findByEmail(string $email):?User;
    public function create(array $data):mixed;
    // public function update(User $user): void;
    // public function delete(User $user): void;
    // public function all(): array;
    // public function paginate(int $limit = 15, int $page = 1): array;
    // public function findBy(string $field, string $value):?User;

}
