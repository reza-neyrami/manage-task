<?php
namespace App\Core\Repository;

use App\Core\Interfaces\UserTask\UserTaskRepositoryInterface;
use App\Model\UserTask;

class UserTaskRepository implements UserTaskRepositoryInterface
{

    private $model;

    public function __construct(UserTask $userTask)
    {
        $this->model = $userTask;
    }

    public function model()
    {
        return $this->model;
    }

    public function assignToUsers(int $taskId, int $userIds)
    {

      return  $this->model->create(['userId' => $userIds, 'taskId' => $taskId]);

    }
    public function deAssignToUsers(int $taskId, int $userIds)
    {

       return $this->model
            ->where('userId', $userIds)->where('taskId', $taskId)
            ->delete();

    }

    public function findUserByTaskId(int $taskId): ?array
    {
        return $this->model->where('userId', $taskId)->first();
    }

    public function getTaskByUserId($taskId)
    {
        return $this->model->where('taskId', $taskId)->getAll();
    }
    public function findTaskByUserId($taskId)
    {
        return $this->model->where('taskId', $taskId)->first();
    }

    public function create(array $data): UserTask
    {
        try {
            return $this->model->create($data);
        } catch (\PDOException $e) {

            throw new \Exception('There was an error creating the user.');
        }
    }

    public function all(): array
    {
        return $this->model->findAll();
    }

    public function paginate(int $page = 1, int $perPage = 15): array
    {
        return $this->model->paginate($page, $perPage);
    }

    public function findBy(string $field, string $value): ?UserTask
    {
        return $this->model->where($field, $value)->first();
    }
    public function getBy(string $field, string $value)
    {
        return $this->model->where($field, $value)->getAll();
    }

    public function update(int $userId, $taskId, array $data): void
    {
        try {

            $this->model->where('userId', $userId)->where('taskId', $taskId)->save();
        } catch (\PDOException $e) {
            throw new \Exception('There was an error updating the user.' . $e->getMessage());
        }
    }
}
