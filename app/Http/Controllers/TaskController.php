<?php

namespace App\Http\Controllers;

use App\Core\Repository\TaskRepository;
use App\Core\Services\JWTApi;
use App\Core\Services\Request;
use App\Core\Services\Response;



class TaskController extends Controller
{
    private $taskRepository;
    protected $request;

    public function __construct(TaskRepository $taskRepository, Request $request)
    {
        $this->taskRepository = $taskRepository;
        $this->request = $request;
    }

    public function show($id)
    {

        $task = $this->taskRepository->findById($id);

        if (!$task) {
            return Response::json(['message' => 'task not found'], 404);
        }
        return $task;
    }

    public function create()
    {
        $task = $this->taskRepository->create([
            'name' => $this->request->get('name'),
            'description' => $this->request->get('description'),
            'startDate' =>  $this->request->get('startDate'),
            'endDate' =>  $this->request->get('endDate'),
            'status' => $this->request->get('status') ?? 'todo',
            'userId' => $this->request->get('userId')
        ]);

        return Response::json($task, 201);
    }

  
}
