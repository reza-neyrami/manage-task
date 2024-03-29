<?php

namespace App\Core\Repository;

use App\Core\Services\Response;
use App\Model\User;

class UserRepository
{
    private $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function model()
    {
        return $this->model;
    }

    public function findById(int $id): User
    {
        $user = $this->model->find($id);
        if (!isset($user)) {
            Response::json(['message' => " User Not Fount"]);
        }
        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        $user = $this->model->where('email', $email)->first();
        if (!isset($user)) {
            Response::json(['message' => " User Not Fount"]);
        }
        return $user;
    }

    // create User  ایجاد  کاربر
    public function create(array $data): User
    {
        try {
            return $this->model->create($data);
        } catch (\PDOException $e) {

            throw new \Exception('There was an error creating the user.');
        }
    }

    public function getTaskByUserId(int $userId)
    {
        // $data = $this->model->where('taskId', $taskId)->where('userId', $userId)->user()->getAll();
        $data = $this->model::find($userId)->tasks();

        if (empty($data)) {
            Response::json(['message' => "اطلاعات در دسترس نیست"]);
        }

        return json_encode($data);
    }

    public function delete(int $id): void
    {
        $user = $this->findById($id);
        if ($user) {
            $user->delete();
        }
    }

    public function all()
    {
        $results = [];
        $this->model->chunk(100, function ($users) use (&$results) {
            foreach ($users as $user) {
                $results[] = $user;
            }
        });
        return $results;
    }

    public function paginate(int $page = 1, int $perPage = 15): array
    {
        return $this->model->paginate($page, $perPage);
        // return $this->model->paginate($page, $perPage);
    }

    public function findBy(string $field, string $value): ?User
    {
        return $this->model->where($field, $value)->first();
    }
    public function getBy(string $field, string $value)
    {
        return $this->model->where($field, $value)->getAll();
    }

    public function update(int $id, array $data): void
    {
        try {

            $this->model::update($id, $data);
        } catch (\PDOException $e) {
            throw new \Exception('There was an error updating the user.' . $e->getMessage());
        }
    }
}
