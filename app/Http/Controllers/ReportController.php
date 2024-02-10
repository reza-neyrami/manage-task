<?php

namespace App\Http\Controllers;

use App\Core\Repository\ReportRepository;
use App\Core\Services\Auth;
use App\Core\Services\Request;
use App\Core\Services\Response;



class TaskController extends Controller
{
    private $reportRepository;
    protected $request;

    public function __construct(ReportRepository $reportRepository, Request $request)
    {
        $this->reportRepository = $reportRepository;
        $this->request = $request;
    }
   
    public function getReport(int $id)
    {
        // echo $id;
        return  $this->reportRepository->findById($id);

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
            $update  =  $this->reportRepository->update(intval($id), $data);
            return Response::json(['message' => 'Report updated successfully.' . $update], 200);
            // Redirect to the Report view or show a success message
        } catch (\Exception $e) {
            // Handle exception here
            return Response::json(['message' => 'There was an error updating the Report. ,' . $e->getMessage()], 500);
        }
    }


    private function getReportUpData()
    {
        return  $this->request->all();
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
            $Reports = $this->reportRepository->all();
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
            'name' => $this->request->get('name'),
            'description' => $this->request->get('description'),
            'startDate' =>  $this->request->get('startDate'),
            'endDate' =>  $this->request->get('endDate'),
            'status' => $this->request->get('status') ?? 'todo',
            'userId' => Auth::user()->id,
        ];
    }
}
