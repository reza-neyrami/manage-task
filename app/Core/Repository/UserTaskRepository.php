<?php
namespace App\Core\Repository;

use App\Core\Interfaces\UserTask\UserTaskRepositoryInterface;
use App\Model\UserTask;

class UserTaskRepository  implements UserTaskRepositoryInterface{

    private $model;

    public function __construct(UserTask $userTask) {
        $this->model = $userTask;
    }

    public function model() {
        return $this->model;
    }

    public function assignToUsers(int $taskId, array $userIds): void
    {
        foreach ($userIds as $userId) {
            $this->model->create(['userId' => $userId, 'taskId' => $taskId]);
        }
    }
}