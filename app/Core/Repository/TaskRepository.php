<?php

namespace App\Core\Repository;

use App\Core\Interfaces\Task\TaskRepositoryInterface;
use App\Core\Services\JWTApi;
use App\Core\Services\Response;
use App\Model\Task;

class TaskRepository implements TaskRepositoryInterface
{
    private $model;

    public function __construct(Task $task)
    {
        $this->model = $task;
    }

    public function findById(int $id): Task
    {
        $task =  $this->model->find($id);
        if(!isset($task)){
             Response::json(['message'=> " User Not Fount"]);
        }
        return $task;
  
    }

    public function getByUserId(int $userId): string
    {
        $task = $this->model->where('userId', $userId)->findAll();
        if(!isset($task)){
            Response::json(['message'=> " User Not Fount"]);
       }
       return json_encode($task);
    }

    
    public function findByUserId(int $userId):?Task
    {
        $task = $this->model->where('userId', $userId)->first();
        if(!isset($task)){
            Response::json(['message'=> " User Not Fount"]);
       }
       return $task;
    }

    public function create(array $data): Task
    {
        try {
            return $this->model->create($data);
        } catch (\PDOException $e) {

            throw new \Exception('There was an error creating the user.');
        }
    }

    public function update(int $id, array $data): void
    {
        $this->model::update($id, $data);
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

    public function model()
    {
        return $this->model;
    }

}
