<?php

namespace App\Core\Repository;

use App\Core\Interfaces\Task\TaskRepositoryInterface;
use App\Model\Task;

class TaskRepository implements TaskRepositoryInterface
{
    private $model;

    public function __construct(Task $task)
    {

        $this->model = $task;
    }

    public function findById(int $id): ?Task
    {
        return  $this->model->find($id);
    }
    
    public function create(array $data): mixed
    {
        return  $this->model->create($data);
    }
}
