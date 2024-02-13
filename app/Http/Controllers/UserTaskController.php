<?php

namespace App\Http\Controllers;

use App\Core\Repository\TaskRepository;
use App\Core\Repository\UserRepository;
use App\Core\Repository\UserTaskRepository;
use App\Core\Services\Auth;
use App\Core\Services\Request;
use App\Model\Task;

class UserTaskController extends BaseController
{
    private $userTaskRepository;
    private $userRepository;
    private $taskRepository;
    protected $request;

    public function __construct(UserTaskRepository $userTaskRepository, UserRepository $userRepository, TaskRepository $taskRepository, Request $request)
    {
        $this->userTaskRepository = $userTaskRepository;
        $this->userRepository = $userRepository;
        $this->taskRepository = $taskRepository;
        $this->request = $request;
    }

    public function userStatusUpdate($taskId)
    {
        $user = Auth::user();
        $task = $this->taskRepository->model()->find($taskId);

        if ($task && in_array($user, $task->users())) {
            $task->status = Task::STATUS_IN_PROGRESS;
            $task->userId = $user->id;
            $task->save();
            return $task;
        } else {
            throw new \Exception("Not Found", 400);
        }
    }

}
