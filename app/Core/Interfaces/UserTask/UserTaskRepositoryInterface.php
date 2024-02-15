<?php

namespace App\Core\Interfaces\UserTask;


interface UserTaskRepositoryInterface
{
    public function model();
    public function assignToUsers(int $taskId, int $userIds);
}
