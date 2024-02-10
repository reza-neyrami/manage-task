<?php

namespace App\Http\Controllers;

use App\Core\Repository\TaskRepository;
use App\Core\Services\Auth;
use App\Core\Services\Request;
use App\Core\Services\Response;



class TaskController extends BaseController
{
    private $taskRepository;
    protected $request;

    public function __construct(TaskRepository $taskRepository, Request $request)
    {
        $this->taskRepository = $taskRepository;
        $this->request = $request;
    }

    public function getTask(int $id)
    {
        // echo $id;
        return  $this->taskRepository->findById($id);
    }

    public function getTasksByUserId(int $userId)
    {
        try {
            $tasks = $this->taskRepository->findByUserId($userId);
            return Response::json($tasks, 200);
            // Render the tasks data in your view
        } catch (\Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error creating the task. ,' . $e->getMessage()], 500);
        }
    }

    public function createTask()
    {
        if (Auth::user()->role !== 'admin') {
            Response::json(['message' => 'شما دسترسی به  این بخش رو ندارید'], 403);
        }
        $task = $this->taskRepository->create($this->getTaskData());
        return $task;
        // return Response::json($task, 201);
    }

    public function updateTask($id)
    {
        try {
            $userid = Auth::user()->id;
            $data = array_merge($this->getTaskUpData(), ['status' => 'todo', 'userId' => $userid]);
            $update  =  $this->taskRepository->update(intval($id), $data);
            return Response::json(['message' => 'Task updated successfully.' . $update], 200);
            // Redirect to the task view or show a success message
        } catch (\Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error updating the task. ,' . $e->getMessage()], 500);
        }
    }


    private function getTaskUpData()
    {
        return  $this->request->all();
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
            'startDate' =>  $this->request->get('startDate'),
            'endDate' =>  $this->request->get('endDate'),
            'status' => $this->request->get('status') ?? 'todo',
            'userId' => Auth::user()->id,
        ];
    }
}
