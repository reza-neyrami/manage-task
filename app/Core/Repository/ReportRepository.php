<?php

namespace App\Core\Repository;

use App\Core\Interfaces\Report\ReportRepositoryInterface;
use App\Core\Services\Response;
use App\Model\Report;

 class ReportRepository implements ReportRepositoryInterface
{
    private $model;

    public function __construct(Report $Report)
    {
        $this->model = $Report;
    }

    public function findById(int $id): Report
    {
        $Report =  $this->model->find($id);
        if(!isset($Report)){
             Response::json(['message'=> " User Not Fount"]);
        }
        return $Report;
  
    }

    public function findByUserId(int $userId): ?Report
    {
        $Report = $this->model->where('userId', $userId)->first();
        if(!isset($Report)){
            Response::json(['message'=> " User Not Fount"]);
       }
       return $Report;
    }

    public function create(array $data): Report
    {
        try {
            return $this->model->create($data);
        } catch (\PDOException $e) {

            throw new \Exception('There was an error creating the user.');
        }
    }

    public function update(int $id, array $data): void
    {
        $this->model::update($id, $data);
    }

    public function delete(int $id): void
    {
        $Report = $this->findById($id);
        if ($Report) {
            $Report->delete();
        }
    }

    public function all(): array
    {
        return $this->model->findAll();
    }

    public function paginate(int $limit = 15, int $page = 1): array
    {
        return $this->model->paginate($page, $limit);
    }

    public function findBy(string $field, string $value): ?Report
    {
        return $this->model->where($field, $value)->first();
    }

    public function model()
    {
        return $this->model;
    }
}
