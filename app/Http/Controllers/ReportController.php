<?php

namespace App\Http\Controllers;

use App\Core\Repository\ReportRepository;
use App\Core\Repository\TaskRepository;
use App\Core\Services\Auth;
use App\Core\Services\Request;
use App\Core\Services\Response;

class ReportController extends BaseController
{
    private $reportRepository;
    private $taskRepository;
    protected $request;

    public function __construct(ReportRepository $reportRepository, TaskRepository $taskRepository, Request $request)
    {
        $this->reportRepository = $reportRepository;
        $this->taskRepository = $taskRepository;
        $this->request = $request;
    }

    // دریافت گزارش مدیریت کاربران بر اساس وضیعت
    public function generateReport()
    {
        // Check if the current user is an admin
        $decoded_token = Auth::user();
        if ($decoded_token->role != 'admin') {
            return Response::json(['message' => " Access Denied"]);
        }

        $endDate = $this->request->endDate;
        $startDate = $this->request->startDate;

        // Check if the date fields are not empty
        if (empty($startDate) || empty($endDate)) {
            return Response::json(['message' => "The date fields cannot be empty"]);
        }

        $tasks = $this->reportRepository->getTasksByDateRange($startDate, $endDate);
        if (!$tasks) {
            return Response::json('no data found');
        }

        // TODO if we want to get CSV file run to method  generateReport
        // $report = $this->reportRepository->generateReport($tasks);
        return json_encode($tasks);
    }

    // دریافت گزارش یک کاربر
    public function getTasksUserByDateRange($startDate, $endDate, $userId)
    {
        $decoded_token = Auth::user();
        if ($decoded_token->role != 'admin') {
            return Response::json(['message' => " Access Denied"]);
        }

        $tasks = $this->reportRepository->getTasksUserByDateRange($startDate, $endDate, $userId);
        // TODO if we want to get CSV file run to method  generateReport
        // $report = $this->reportRepository->generateReport($tasks);

        // Return the report as a CSV file
        return json_encode($tasks);
    }

    public function getReport(int $id)
    {
        // echo $id;
        return $this->reportRepository->findById($id);

    }

    public function getReportsByUserId(int $userId)
    {
        try {
            $Reports = $this->reportRepository->findByUserId($userId);
            return Response::json($Reports, 200);
            // Render the Reports data in your view
        } catch (\Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error creating the Report. ,' . $e->getMessage()], 500);
        }
    }

    public function createReport()
    {

        $Report = $this->reportRepository->create($this->getReportData());
        return $Report;
        // return Response::json($Report, 201);
    }

    public function updateReport($id)
    {
        try {
            $userid = Auth::user()->id;
            $data = array_merge($this->getReportUpData(), ['status' => 'todo', 'userId' => $userid]);
            $this->reportRepository->update(intval($id), $data);
            return Response::json(['message' => 'Report updated successfully.'], 200);
            // Redirect to the Report view or show a success message
        } catch (\Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error updating the Report. ,' . $e->getMessage()], 500);
        }
    }

    private function getReportUpData()
    {
        return $this->request->all();
    }

    public function deleteReport(int $id)
    {
        try {
            $this->reportRepository->delete($id);
            return Response::json(['message' => 'Report deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error deleting the Report. ,' . $e->getMessage()], 500);
        }
    }

    public function getAllReports()
    {
        try {
            $Reports = $this->reportRepository->paginate($this->request->page ?? 1, $this->request->perPage ?? 10);
            return Response::json($Reports, 200);
        } catch (\Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error getting the Reports. ,' . $e->getMessage()], 500);
        }
    }

    public function getPaginatedReports(int $limit = 15, int $page = 1)
    {
        try {
            $Reports = $this->reportRepository->paginate($limit, $page);
            return Response::json($Reports, 200);
            // Render the Reports data in your view
        } catch (\Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error getting the Reports. ,' . $e->getMessage()], 500);
        }
    }

    public function getReportBy(string $field, string $value)
    {
        try {
            $Report = $this->reportRepository->findBy($field, $value);
            return Response::json($Report, 200);
            // Render the Report data in your view
        } catch (\Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error getting the Report. ,' . $e->getMessage()], 500);
        }
    }

    private function getReportData()
    {
        return [
            'filename' => $this->request->filename,
            'taskId' => $this->request->status ?? 'todo',
            'userId' => Auth::user()->id,
        ];
    }

    // دریافت گزارشات مربوط به هر تسک کاربر
    public function getLogsWithTaskId(int $taskId)
    {
        $userId = Auth::user()->id;
        $report = $this->reportRepository->model()
            ->where('taskId', $taskId)
            ->where('userId', $userId)->getAll();
        return json_encode($report);
    }

    public function listDirectoryLogs(){
        if(Auth::user()->role !='admin') {
            return Response::json(['message'=> 'UnAuthorize'],403);
        }
        $dir =  $_SERVER['DOCUMENT_ROOT'] . "/public/uploads/";
        $files = scandir($dir);

        $fileList = [];
    
        foreach($files as $file){
            if($file == '.' || $file == '..') continue;
    
            $fileList[] = [
                'name' => $file,
                'url' => '/public/uploads/'.$file,
            ];
        }
    
        echo json_encode($fileList);
        
    }

}
