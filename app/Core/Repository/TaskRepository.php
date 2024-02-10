<?php

namespace App\Core\Repository;

use App\Core\Interfaces\Task\TaskRepositoryInterface;
use App\Core\Services\Response;
use App\Model\Task;

class TaskRepository implements TaskRepositoryInterface
{
    private $model;

    public function __construct(Task $task) {
        $this->model = $task;
    }

    public function model() {
        return $this->model;
    }

    public function findById(int $id): ?Task
    {
        return $this->model->find($id);
    }

    public function findByUserId(int $userId): ?Task
    {
        return $this->model->where('userId', $userId)->first();
    }

    public function create(array $data): Task
    {
        return $this->model->create($data);

    }

    public function update(int $id, array $data): void
    {
        $task = $this->findById($id);
        if ($task) {
            foreach ($data as $key => $value) {
                if (in_array($key, $this->model->fillable)) {
                    $task->{$key} = $value;
                }
            }
            $task->save();
        }
    }

    public function delete(int $id): void
    {
        $task = $this->findById($id);
        if ($task) {
            $task->delete();
        }
    }

    public function all(): array
    {
        return $this->model->findAll();
    }

    public function paginate(int $limit = 15, int $page = 1): array
    {
        return $this->model->paginate($page, $limit);
    }

    public function findBy(string $field, string $value): ?Task
    {
        return $this->model->where($field, $value)->first();
    }
}


