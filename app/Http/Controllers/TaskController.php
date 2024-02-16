<?php

namespace App\Http\Controllers;

use App\Core\Repository\TaskRepository;
use App\Core\Repository\UserTaskRepository;
use App\Core\Services\Auth;
use App\Core\Services\Request;
use App\Core\Services\Response;
use App\Model\Task;
use Exception;

class TaskController extends BaseController
{
    private $taskRepository;
    protected $userTaskRepository;
    protected $request;

    public function __construct(
        TaskRepository $taskRepository,
        UserTaskRepository $userTaskRepository,
        Request $request
    ) {
        $this->taskRepository = $taskRepository;
        $this->userTaskRepository = $userTaskRepository;
        $this->request = $request;
    }

    // دریافت یک تسک بر اساس ایدی
    public function getTask(int $id)
    {
        // echo $id;
        return $this->taskRepository->findById($id);
    }

    // get task by Auth Id
    public function taskByAuthId()
    {
        $task = $this->taskRepository->getByUserId(Auth::user()->id);
        if (!$task) {
            Response::json(["message" => "no any task"], 404);
        }
        return $task;
    }

    // get tasks by userId just only 
    public function getTasksByUserId(int $userId)
    {
        try {
            
            $tasks = $this->taskRepository->findByUserId($userId);
            Response::json($tasks, 200);
            // Render the tasks data in your view
        } catch (Exception $e) {
            // Handle exception here
            Response::json(['message' => 'There was an error creating the task. ,' . $e->getMessage()], 500);
        }
    }

    public function createTask()
    {
        if (Auth::user()->role !== 'admin') {
            Response::json(['message' => 'شما دسترسی به  این بخش رو ندارید'], 403);
        }
        $data = $this->getTaskData();
        if (isset($data['status']) && $data['status'] != Task::STATUS_TODO) {
            Response::json(['message' => 'شما دسترسی به  این بخش رو ندارید'], 403);
        }
        $task = $this->taskRepository->create($data);
        return json_encode($task);
    }
    public function updateTask($id)
    {
        try {
            $userid = Auth::user();
            $data = $this->getTaskUpData();
            $task = $this->taskRepository->findById(intval($id));
            if ($userid->role !== 'admin' || $userid->id != $task->userId) {
                throw new Exception('شما به این بخش دسترسی ندارید ');
            }
            // تغییر وضعیت
            $task->changeStatus($data['status']);

            $data = array_merge($data, ["userId" => $userid->id]);

            $this->taskRepository->update(intval($id), $data);

            return Response::json(['message' => 'Task updated successfully.'], 200);
            // Redirect to the task view or show a success message
        } catch (Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error updating the task. ,' . $e->getMessage()], 500);
        }
    }

    private function getTaskUpData()
    {
        return $this->request->all();
    }

    public function deleteTask(int $id)
    {
        try {
            $this->taskRepository->delete($id);
            return Response::json(['message' => 'Task deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error deleting the task. ,' . $e->getMessage()], 500);
        }
    }

    public function getAllTasks()
    {
        try {
            $tasks = $this->taskRepository->all();
            return Response::json($tasks, 200);
        } catch (\Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error getting the tasks. ,' . $e->getMessage()], 500);
        }
    }

    public function getPaginatedTasks(int $limit = 15, int $page = 1)
    {
        try {
            $tasks = $this->taskRepository->paginate($limit, $page);
            return Response::json($tasks, 200);
            // Render the tasks data in your view
        } catch (\Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error getting the tasks. ,' . $e->getMessage()], 500);
        }
    }

    public function getTaskBy(string $field, string $value)
    {
        try {
            $task = $this->taskRepository->findBy($field, $value);
            return Response::json($task, 200);
            // Render the task data in your view
        } catch (\Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error getting the task. ,' . $e->getMessage()], 500);
        }
    }

    private function getTaskData()
    {
        return [
            'name' => $this->request->name,
            'description' => $this->request->description,
            'startDate' => $this->request->startDate,
            'endDate' => $this->request->endDate,
            'status' => $this->request->status ?? 'todo',
            'userId' => Auth::user()->id,
        ];
    }

    //assigned one task to several user with access role
    public function assignTask($taskId)
    {
        // Check if the current user is an admin
        $userId = Auth::user();
        if ($userId->role != 'admin') {
            return Response::json(['message' => " Access Denied"]);
        }
        $userIds = $this->request->userIds;
        foreach ($userIds as $userId) {
            $this->userTaskRepository->assignToUsers($taskId, $userId);
        }

        return Response::json(['message' => " Task assigned successfully"]);
    }
}
