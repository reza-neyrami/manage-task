<?php

namespace App\Http\Controllers;

use App\Core\Repository\ReportRepository;
use App\Core\Repository\TaskRepository;
use App\Core\Repository\UserTaskRepository;
use App\Core\Services\Auth;
use App\Core\Services\Request;

class UserTaskController extends BaseController
{
    private $userTaskRepository;
    private $reportRipository;
    private $taskRepository;
    protected $request;

    public function __construct(UserTaskRepository $userTaskRepository, ReportRepository $reportRipository, TaskRepository $taskRepository, Request $request)
    {
        $this->userTaskRepository = $userTaskRepository;
        $this->reportRipository = $reportRipository;
        $this->taskRepository = $taskRepository;
        $this->request = $request;
    }

    public function userStatusUpdate($taskId)
    {
        $user = Auth::user();
        $task = $this->taskRepository->model()->find($taskId);

        if ($task && in_array($user, $task->users())) {
            $task->changeStatus($this->request->status);
            $task->status = $this->request->status;
            $task->userId = $user->id;
            $task->save();
            $this->reportRipository->create([
                "taskId" => $task->id,
                "userId" => $user->id,
                'name' => $this->request->name,
                'description' => $this->request->description,
                'filename' => $this->request->banner,
            ]);

            return json_encode($task->toArray());
        } else {
            throw new \Exception("Not Found", 400);
        }
    }

}
