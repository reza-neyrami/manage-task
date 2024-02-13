<?php

namespace App\Http\Controllers;

use App\Core\Repository\UserTaskRepository;
use App\Core\Services\Auth;
use App\Core\Services\Request;

class UserTaskController extends BaseController
{
    private $userTaskRepository;
    protected $request;

    public function __construct(UserTaskRepository $userTaskRepository, Request $request)
    {
        $this->userTaskRepository = $userTaskRepository;
        $this->request = $request;
    }

    public function getUserTasks($taskId)
    {
        $user = Auth::user();
        $userTask = $this->userTaskRepository
            ->getUserByTaskId($taskId, $user->id);
            return $userTask;
    }
}
