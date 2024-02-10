<?php

namespace App\Core\Interfaces\Task;

use App\Model\Task;

interface TaskRepositoryInterface
{
    public function model();
    public function findById(int $id): ?Task;
    public function findByuserId(int $userId): ?Task;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
    public function all(): array;
    public function paginate(int $limit = 15, int $page = 1): array;
    public function findBy(string $field, string $value): ?Task;

}
