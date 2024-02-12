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

    public function getTask(int $id)
    {
        // echo $id;
        return $this->taskRepository->findById($id);
    }

    public function taskByAuthId()
    {
        $task = $this->taskRepository->getByUserId(Auth::user()->id);
        if (!$task) {
            Response::json(["message" => "no any task"], 404);
        }
        return $task;
    }

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
            throw new Exception('وضعیت باید در حالت "برای انجام" باشد.');
        }
        $task = $this->taskRepository->create($data);
        return $task;
    }

    public function updateTask($id)
    {
        try {
            $userid = Auth::user();
            $data = $this->getTaskUpData();
            $task = $this->taskRepository->findById(intval($id));
    
            if ($data['status'] == Task::STATUS_IN_PROGRESS && $task->status != Task::STATUS_TODO) {
                throw new Exception('وظیفه باید در حالت "برای انجام" باشد تا بتوان آن را به حالت "در حال انجام" تغییر داد.');
            } elseif ($data['status'] == Task::STATUS_DONE && $task->status != Task::STATUS_IN_PROGRESS) {
                throw new Exception('وظیفه باید در حالت "در حال انجام" باشد تا بتوان آن را به حالت "انجام شده" تغییر داد.');
            } elseif ($data['status'] == Task::STATUS_TODO) {
                throw new Exception('وظیفه نمی‌تواند به حالت "برای انجام" برگردد.');
            }
    
            if ($data['status'] == Task::STATUS_IN_PROGRESS) {
                $task->start();
            } elseif ($data['status'] == Task::STATUS_DONE) {
                $task->finish();
            }
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
            'name' => $this->request->get('name'),
            'description' => $this->request->get('description'),
            'startDate' => $this->request->get('startDate'),
            'endDate' => $this->request->get('endDate'),
            'status' => $this->request->get('status') ?? 'todo',
            'userId' => Auth::user()->id,
        ];
    }

    //assigned one task to several user with access role
    public function assignTask($taskId)
    {
        // Check if the current user is an admin
        $decoded_token = Auth::user();
        if ($decoded_token->role != 'admin') {
            return Response::json(['message' => " Access Denied"]);
        }
        $userIds = $this->request->input('userIds');
        foreach ($userIds as $userId) {
            $this->userTaskRepository->assignToUsers($taskId, $userId);
        }

        return Response::json(['message' => " Task assigned successfully"]);
    }
}
