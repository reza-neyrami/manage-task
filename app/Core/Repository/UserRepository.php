<?php

namespace App\Core\Repository;

use App\Core\Interfaces\User\UserRepositoryInterface;
use App\Core\Services\Response;
use App\Model\User;


class UserRepository implements UserRepositoryInterface
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
        $user =  $this->model->find($id);
        if(!isset($user)){
             Response::json(['message'=> " User Not Fount"]);
        }
        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        $user =  $this->model->where('email', $email)->first();
        if(!isset($user)){
            Response::json(['message'=> " User Not Fount"]);
       }
       return $user;
    }

    public function create(array $data): User
    {
        try {
            return $this->model->create($data);
        } catch (\PDOException $e) {

            throw new \Exception('There was an error creating the user.');
        }
    }


    public function delete(int $id): void
    {
        $user = $this->findById($id);
        if ($user) {
            $user->delete();
        }
    }

    public function all(): array
    {
          return $this->model->findAll();
    }

    public function paginate(int $page = 1, int $perPage = 15): array
    {
        return $this->model->paginate($page, $perPage);
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
